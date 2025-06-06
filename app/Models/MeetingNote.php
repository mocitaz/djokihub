<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class MeetingNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
        'notes_content', // CHANGED from notes_html to match server validation and JS
        'meeting_datetime',
        'location',
        'status',
        'created_by_user_id',
        'project_id', 
    ];

    protected $casts = [
        'meeting_datetime' => 'datetime',
    ];

    /**
     * The user who created the meeting note.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * The project this meeting note might be associated with.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * The participants of the meeting.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_note_user', 'meeting_note_id', 'user_id')->withTimestamps();
    }

    /**
     * The attachments for the meeting note.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(MeetingAttachment::class);
    }

    /**
     * Accessor to get formatted meeting date.
     */
    public function getFormattedMeetingDateAttribute()
    {
        return $this->meeting_datetime ? Carbon::parse($this->meeting_datetime)->isoFormat('D MMMM YYYY') : 'N/A';
    }

    /**
     * Accessor to get formatted meeting time.
     */
    public function getFormattedMeetingTimeAttribute()
    {
        return $this->meeting_datetime ? Carbon::parse($this->meeting_datetime)->isoFormat('HH:mm') : 'N/A';
    }

    /**
     * Accessor to get the notes_html content (for compatibility if view still uses it sometimes)
     * but primary storage and validation is notes_content
     */
    public function getNotesHtmlAttribute()
    {
        return $this->notes_content;
    }
}
