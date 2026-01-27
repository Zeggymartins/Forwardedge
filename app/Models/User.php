<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Enforce single admin rule on create/update
        static::saving(function ($user) {
            if ($user->role === 'admin') {
                // Check if another admin exists
                $existingAdmin = self::where('role', 'admin')
                    ->where('id', '!=', $user->id)
                    ->first();

                if ($existingAdmin) {
                    // Another admin exists, prevent this user from being admin
                    $user->role = 'user';
                }
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'verification_token_expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Generate a unique 10-character enrollment ID
     */
    public static function generateEnrollmentId(): string
    {
        do {
            $id = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        } while (self::where('enrollment_id', $id)->exists());

        return $id;
    }

    /**
     * Check if verification token is valid and not expired
     */
    public function hasValidVerificationToken(): bool
    {
        return $this->verification_token
            && $this->verification_token_expires_at
            && $this->verification_token_expires_at->isFuture();
    }

    /**
     * Get the user's scholarship applications
     */
    public function scholarshipApplications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }

    /**
     * Get location/country from scholarship application or user nationality
     */
    public function getCountryAttribute(): ?string
    {
        // First check user's nationality from verification
        if ($this->nationality) {
            return $this->nationality;
        }

        // Fallback to scholarship application location
        $application = $this->scholarshipApplications()->latest()->first();
        if ($application) {
            $location = $application->form_data['personal']['location'] ?? null;
            if ($location) {
                // Extract country from location (last part after comma)
                $parts = array_map('trim', explode(',', $location));
                return end($parts);
            }
        }

        return null;
    }
}
