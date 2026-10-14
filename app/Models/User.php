<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacherSubjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

      // ============ NEW: Photo URL Accessor ============
    /**
     * Get the full URL to the user's photo
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path) {
            return null;
        }

        // Public folder check
        $publicPath = public_path($this->photo_path);
        if (file_exists($publicPath)) {
            return asset($this->photo_path);
        }

        // Storage fallback
        $storagePath = storage_path('app/public/' . $this->photo_path);
        if (file_exists($storagePath)) {
            return asset('storage/' . $this->photo_path);
        }

        return null;
    }

    /**
     * Get initials from name (for avatar fallback)
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
            if (strlen($initials) >= 2) break;
        }

        return $initials ?: 'U';
    }

    /**
     * Check if user has a photo
     */
    public function getHasPhotoAttribute()
    {
        return $this->photo_url !== null;
    }

    /**
     * Get avatar color (deterministic based on name)
     */
    public function getAvatarColorAttribute()
    {
        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
        $hash = crc32($this->name);
        return $colors[$hash % count($colors)];
    }

    
}