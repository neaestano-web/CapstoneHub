<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'first_name', 'last_name', 'email', 'password', 'role', 'student_id_number', 'id_picture_front', 'id_picture_back', 'registration_status', 'registration_rejection_reason'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
        ];
    }

    /**
     * Generate the full name from first_name and last_name.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        return $this->name ?? '';
    }

    /**
     * Get clean First Name and Last Name format (e.g. "Juvelyn Pahayahay").
     */
    public function getFirstLastNameAttribute(): string
    {
        if (!empty($this->first_name) && !empty($this->last_name)) {
            return trim($this->first_name . ' ' . $this->last_name);
        }

        $raw = trim($this->name ?? '');
        if (str_contains($raw, ',')) {
            $parts = explode(',', $raw, 2);
            $lastName = trim($parts[0]);
            $firstPart = trim($parts[1]);
            // Remove trailing single initial like " G." or " G"
            $firstPart = preg_replace('/\s+[A-Za-z]\.?$/', '', $firstPart);
            return trim($firstPart . ' ' . $lastName);
        }

        return $raw;
    }

    /**
     * Get single letter initial for avatar.
     */
    public function getAvatarInitialAttribute(): string
    {
        $name = $this->first_last_name;
        return $name !== '' ? strtoupper(mb_substr($name, 0, 1)) : 'U';
    }

    public function researches()
    {
        return $this->hasMany(
            Research::class
        );
    }

    /**
     * Check if the user's registration is approved.
     */
    public function isApproved(): bool
    {
        return $this->registration_status === 'approved';
    }

    /**
     * Check if the user's registration is pending.
     */
    public function isPending(): bool
    {
        return $this->registration_status === 'pending';
    }

    /**
     * Check if the user's registration is rejected.
     */
    public function isRejected(): bool
    {
        return $this->registration_status === 'rejected';
    }
}
