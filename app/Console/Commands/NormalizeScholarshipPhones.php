<?php

namespace App\Console\Commands;

use App\Models\ScholarshipApplication;
use App\Services\PhoneNumberNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NormalizeScholarshipPhones extends Command
{
    protected $signature = 'scholarships:normalize-phones {--from=} {--limit=0} {--dry-run} {--no-twilio}';

    protected $description = 'Normalize phone numbers in scholarship applications using libphonenumber.';

    public function handle(PhoneNumberNormalizer $normalizer): int
    {
        $query = ScholarshipApplication::query();

        if ($from = $this->option('from')) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        }

        if ($limit = (int) $this->option('limit')) {
            $query->limit($limit);
        }

        $updated = 0;
        $checked = 0;
        $dryRun = (bool) $this->option('dry-run');
        $useTwilio = ! $this->option('no-twilio');

        $query->orderBy('id')
            ->chunkById(200, function ($rows) use ($normalizer, &$updated, &$checked, $dryRun) {
                foreach ($rows as $app) {
                    $checked++;
                    $data = $app->form_data ?? [];
                    $location = $data['personal']['location'] ?? null;
                    $rawPhone = $data['personal']['phone_raw']
                        ?? $data['contact']['phone_raw']
                        ?? $data['personal']['phone']
                        ?? $data['contact']['phone']
                        ?? null;

                    if (!$rawPhone) {
                        continue;
                    }

                    $phoneMeta = $normalizer->normalize($rawPhone, $location, null, $useTwilio);
                    $normalized = $phoneMeta['e164'] ?? $rawPhone;

                    $data['personal']['phone'] = $normalized;
                    $data['personal']['phone_raw'] = $rawPhone;
                    $data['personal']['phone_meta'] = $phoneMeta;
                    if (!empty($data['contact'])) {
                        $data['contact']['phone'] = $normalized;
                        $data['contact']['phone_raw'] = $rawPhone;
                        $data['contact']['phone_meta'] = $phoneMeta;
                    }

                    if (!$dryRun) {
                        $app->form_data = $data;
                        $app->save();

                        if ($app->user && (empty($app->user->phone) || $app->user->phone === '—')) {
                            $app->user->phone = $normalized;
                            $app->user->save();
                        }
                    }

                    $updated++;
                }
            });

        $this->info("Checked: {$checked}");
        $this->info(($dryRun ? 'Would update' : 'Updated') . ": {$updated}");

        return Command::SUCCESS;
    }
}
