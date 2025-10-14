<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseDetails extends Model
{
    protected $fillable = ['course_id', 'type', 'content', 'image', 'sort_order'];

    // Don't cast content; we normalize/type-switch in accessors.
    protected $casts = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /** Store arrays as JSON, strings as-is */
    public function setContentAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['content'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif (is_null($value)) {
            $this->attributes['content'] = null;
        } else {
            $this->attributes['content'] = (string) $value;
        }
    }

    /** Always give Blade a safe, normalized shape based on type */
    public function getContentAttribute($value): ?string
    {
        $type = $this->attributes['type'] ?? null;

        // Helper to try-decoding JSON
        $decode = function ($v) {
            if (!is_string($v)) return null;
            $d = json_decode($v, true);
            return json_last_error() === JSON_ERROR_NONE ? $d : null;
        };

        // ---------- Headings & Paragraphs: return PLAIN STRING ----------
        if ($type === 'heading' || $type === 'paragraph') {
            // If it was saved as JSON {"title": "...","body":"..."}
            $d = $decode($value);
            if (is_array($d)) {
                // Prefer body text; fallback to title
                $text = '';
                if (isset($d['body']) && is_string($d['body'])) {
                    $text = $d['body'];
                } elseif (isset($d['title']) && is_string($d['title'])) {
                    $text = $d['title'];
                } else {
                    // Any first scalar string in the array
                    foreach ($d as $v) {
                        if (is_string($v)) {
                            $text = $v;
                            break;
                        }
                    }
                }
                return $text;
            }
            // Was already a string
            return is_string($value) ? $value : '';
        }

        // ---------- Lists: return JSON array of strings ----------
        if ($type === 'list' || $type === 'lists') {
            $d = $decode($value);

            // Already a numeric array
            if (is_array($d) && array_is_list($d)) {
                $items = array_values(array_filter(array_map('strval', $d), fn($s) => $s !== ''));
                return json_encode($items, JSON_UNESCAPED_UNICODE);
            }

            // Associative with 'items' or 'content'
            if (is_array($d)) {
                $items = [];
                if (isset($d['items']) && is_array($d['items'])) $items = $d['items'];
                if (isset($d['content']) && is_array($d['content'])) $items = $d['content'];
                $items = array_values(array_filter(array_map('strval', $items), fn($s) => $s !== ''));
                return json_encode($items, JSON_UNESCAPED_UNICODE);
            }

            // Plain string: split into items
            if (is_string($value) && trim($value) !== '') {
                $parts = preg_split('/\r\n|\r|\n|,/', $value);
                $parts = array_values(array_filter(array_map(fn($s) => trim((string)$s), $parts ?: []), fn($s) => $s !== ''));
                return json_encode($parts, JSON_UNESCAPED_UNICODE);
            }

            return json_encode([], JSON_UNESCAPED_UNICODE);
        }

        // ---------- Images: return JSON array of image paths ----------
        if ($type === 'image' || $type === 'images') {
            $d = $decode($value);

            if (is_array($d)) {
                // Normalize to a numeric list of strings
                if (!array_is_list($d)) {
                    $d = array_values(array_filter($d, fn($v) => is_string($v)));
                }
                $d = array_values(array_filter($d, fn($v) => is_string($v) && $v !== ''));
                return json_encode($d, JSON_UNESCAPED_UNICODE);
            }

            if (!empty($this->attributes['image'])) {
                return json_encode([$this->attributes['image']], JSON_UNESCAPED_UNICODE);
            }

            return json_encode([], JSON_UNESCAPED_UNICODE);
        }

        // ---------- Features: return JSON array of {heading, description} ----------
        if ($type === 'features') {
            $d = $decode($value);
            $norm = [];

            if (is_array($d)) {
                foreach ($d as $feat) {
                    if (is_array($feat)) {
                        $norm[] = [
                            'heading' => isset($feat['heading']) ? (string)$feat['heading'] : '',
                            'description' => isset($feat['description']) ? (string)$feat['description'] : '',
                        ];
                    } elseif (is_string($feat)) {
                        $norm[] = ['heading' => $feat, 'description' => ''];
                    }
                }
            } elseif (is_string($value) && $value !== '') {
                $norm[] = ['heading' => $value, 'description' => ''];
            }

            return json_encode(array_values($norm), JSON_UNESCAPED_UNICODE);
        }

        // Other / unknown types: return raw string
        return is_string($value) ? $value : '';
    }

    // ---------- Optional helpers if you ever want them ----------
    public function getListItemsAttribute(): array
    {
        if (!is_string($this->content)) return [];
        $d = json_decode($this->content, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($d)) ? $d : [];
    }

    public function getImagesListAttribute(): array
    {
        if (!is_string($this->content)) return [];
        $d = json_decode($this->content, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($d)) ? array_values($d) : [];
    }

    public function getFeaturesListAttribute(): array
    {
        if (!is_string($this->content)) return [];
        $d = json_decode($this->content, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($d)) ? $d : [];
    }
}
