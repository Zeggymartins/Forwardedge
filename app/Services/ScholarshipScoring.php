<?php

namespace App\Services;

use App\Models\ScholarshipApplication;

class ScholarshipScoring
{
    protected array $caps = [];
    protected array $categoryTotals = [];

    public function score(ScholarshipApplication $application): array
    {
        $config = config('scholarship_scoring', []);
        $form = $application->form_data ?? [];
        $this->caps = $config['category_caps'] ?? [];
        $this->categoryTotals = array_fill_keys(array_keys($this->caps), 0);

        $reasons = [];
        $score = 0;

        foreach ($config['weights'] ?? [] as $rule) {
            $value = $this->value($form, $rule['path'] ?? '');
            if ($value === null) {
                continue;
            }
            $map = $rule['values'] ?? [];
            if (isset($map[$value])) {
                $this->applyPoints($score, $map[$value], $rule['category'] ?? null, $rule['label'] ?? $rule['path'], $reasons);
            }
        }

        foreach ($config['array_counts'] ?? [] as $rule) {
            $items = (array) $this->value($form, $rule['path'] ?? '', []);
            $count = count(array_filter($items));
            if ($count === 0) {
                continue;
            }
            $points = min($count * ($rule['per_item'] ?? 0), $rule['max_score'] ?? PHP_INT_MAX);
            if ($points > 0) {
                $this->applyPoints($score, $points, $rule['category'] ?? null, $rule['label'] ?? $rule['path'], $reasons);
            }
        }

        foreach ($config['text_lengths'] ?? [] as $rule) {
            $text = (string) $this->value($form, $rule['path'] ?? '', '');
            if (mb_strlen($text) >= ($rule['min'] ?? INF)) {
                $this->applyPoints($score, $rule['score'] ?? 0, $rule['category'] ?? null, $rule['label'] ?? $rule['path'], $reasons);
            }
        }

        foreach ($config['keyword_sets'] ?? [] as $rule) {
            $text = mb_strtolower((string) $this->value($form, $rule['path'] ?? '', ''));
            if ($text === '') {
                continue;
            }
            $hits = 0;
            foreach ($rule['keywords'] ?? [] as $keyword) {
                if (str_contains($text, mb_strtolower($keyword))) {
                    $hits++;
                }
            }
            if ($hits > 0) {
                $points = min($hits * ($rule['score_per_hit'] ?? 0), $rule['max_score'] ?? PHP_INT_MAX);
                $this->applyPoints($score, $points, $rule['category'] ?? null, $rule['label'] ?? $rule['path'], $reasons);
            }
        }

        foreach ($config['penalties'] ?? [] as $rule) {
            $value = $this->value($form, $rule['path'] ?? '');
            $shouldApply = false;

            if (isset($rule['matches']) && in_array($value, (array) $rule['matches'], true)) {
                $shouldApply = true;
            }

            if (!$shouldApply && isset($rule['blank_when'])) {
                $allMatch = true;
                foreach ($rule['blank_when'] as $path => $expected) {
                    if ($this->value($form, $path) !== $expected) {
                        $allMatch = false;
                        break;
                    }
                }
                if ($allMatch && blank($value)) {
                    $shouldApply = true;
                }
            }

            if ($shouldApply) {
                $this->applyPoints($score, $rule['score'] ?? 0, $rule['category'] ?? null, $rule['label'] ?? $rule['path'], $reasons);
            }
        }

        $autoDecision = null;
        foreach ($config['disqualifiers'] ?? [] as $rule) {
            $value = $this->value($form, $rule['path'] ?? '');
            if ($value !== null && in_array($value, (array) ($rule['matches'] ?? []), true)) {
                $autoDecision = 'reject';
                if (!empty($rule['note'])) {
                    $reasons[] = $rule['note'];
                }
                break;
            }
        }

        $softDecision = null;
        foreach ($config['soft_rules'] ?? [] as $rule) {
            $matches = true;
            foreach ($rule['conditions'] ?? [] as $condition) {
                $actual = $this->value($form, $condition['path'] ?? '');
                if (isset($condition['equals']) && $actual !== $condition['equals']) {
                    $matches = false;
                    break;
                }
                if (isset($condition['in']) && !in_array($actual, (array) $condition['in'], true)) {
                    $matches = false;
                    break;
                }
            }
            if ($matches) {
                $softDecision = $rule['decision'] ?? null;
                if (!empty($rule['note'])) {
                    $reasons[] = $rule['note'];
                }
            }
        }

        if ($autoDecision !== 'reject') {
            if ($softDecision === 'pending') {
                $autoDecision = 'pending';
            } else {
                $thresholdApprove = $config['auto_approve_threshold'] ?? 70;
                $thresholdReject = $config['auto_reject_threshold'] ?? 30;

                if ($score >= $thresholdApprove) {
                    $autoDecision = 'approve';
                } elseif ($score <= $thresholdReject) {
                    $autoDecision = 'reject';
                }
            }
        }

        return [
            'score'    => (int) round($score),
            'decision' => $autoDecision,
            'reasons'  => $reasons,
        ];
    }

    protected function applyPoints(float &$totalScore, float $points, ?string $category, string $label, array &$reasons): void
    {
        if ($points === 0.0) {
            return;
        }

        $value = $points;

        if ($category) {
            $this->categoryTotals[$category] ??= 0;
            $cap = $this->caps[$category] ?? null;

            if ($cap !== null && $value > 0) {
                $used = $this->categoryTotals[$category];
                $available = max($cap - $used, 0);

                if ($available <= 0) {
                    $value = 0.0;
                } else {
                    $value = min($value, $available);
                    $this->categoryTotals[$category] = $used + $value;
                }
            } elseif ($value > 0) {
                $this->categoryTotals[$category] += $value;
            }
        }

        if ($value === 0.0) {
            return;
        }

        $totalScore += $value;
        $signed = $value > 0 ? '+' . $value : (string) $value;
        $reasons[] = "{$label} ({$signed})";
    }

    protected function value(array $form, ?string $path, $default = null)
    {
        if (!$path) {
            return $default;
        }

        return data_get($form, $path, $default);
    }
}
