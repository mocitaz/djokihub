<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Untuk logging

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path', // Pastikan ini ada dan fillable
        'availability_status',
        'phone_number',
        'location',
        'linkedin_url',
        'github_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')->withTimestamps();
    }

    public function meetingNotes(): BelongsToMany
    {
        // Pastikan model MeetingNote ada dan namespace-nya benar
        // Jika belum ada, Anda bisa mengomentari relasi ini untuk sementara
        // return $this->belongsToMany(MeetingNote::class, 'meeting_note_user', 'user_id', 'meeting_note_id')->withTimestamps();
        // Menggunakan config untuk fleksibilitas jika nama model berbeda
        // Ganti 'App\Models\MeetingNote' dengan path yang benar jika berbeda
        if (class_exists(\App\Models\MeetingNote::class)) {
            return $this->belongsToMany(\App\Models\MeetingNote::class, 'meeting_note_user', 'user_id', 'meeting_note_id')->withTimestamps();
        }
        // Fallback jika model tidak ada untuk menghindari error, atau handle sesuai kebutuhan
        return $this->belongsToMany(Model::class, 'meeting_note_user', 'user_id', 'meeting_note_id')->withTimestamps();


    }

    public function createdMeetingNotes()
    {
        // Pastikan model MeetingNote ada dan namespace-nya benar
        // Jika belum ada, Anda bisa mengomentari relasi ini untuk sementara
        // return $this->hasMany(MeetingNote::class, 'created_by_user_id');
        if (class_exists(\App\Models\MeetingNote::class)) {
            return $this->hasMany(\App\Models\MeetingNote::class, 'created_by_user_id');
        }
        return $this->hasMany(Model::class, 'created_by_user_id');
    }


    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
       public function getProfilePhotoUrlAttribute()
{
        \Log::info("Checking profile photo path for user {$this->id}: {$this->profile_photo_path}");
        if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
            $url = Storage::disk('public')->url($this->profile_photo_path);
            \Log::info("Profile photo URL for user {$this->id}: {$url}");
            return $url;
        }
        \Log::info("Profile photo not found for user {$this->id}, using fallback.");
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?: 'X') . '&color=FFFFFF&background=4F46E5&size=128&font-size=0.33&bold=true&rounded=true';
    }
    
    /**
     * Get the count of completed projects for the user.
     */
    public function getCompletedProjectsCountAttribute(): int
    {
        // Always query the database for the accurate count of completed projects,
        // regardless of what is eager-loaded for the 'projects' relation for display purposes.
        return $this->projects()->where('status', 'Completed')->count();
    }

    /**
     * Get the count of assigned tasks (dummy logic).
     */
    public function getAssignedTasksCountAttribute(): int
    {
        $activeProjects = $this->projects()->whereNotIn('status', ['Completed', 'Cancelled'])->count();
        return $activeProjects * rand(1, 2) + rand(0, 1); // Logika acak yang lebih kecil
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url', 
        'completed_projects_count' 
        // 'assigned_tasks_count' // Uncomment jika Anda ingin ini selalu ada
    ];
}
