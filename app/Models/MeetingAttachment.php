<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MeetingAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_note_id',
        'file_name', // Original file name
        'file_path', // Path in storage (e.g., meeting_attachments/1/somefile.pdf)
        'file_type', // MIME type
        'file_size', // In bytes
    ];

    /**
     * The meeting note this attachment belongs to.
     */
    public function meetingNote(): BelongsTo
    {
        return $this->belongsTo(MeetingNote::class);
    }

    /**
     * Accessor for the public URL of the attachment.
     *
     * @return string|null
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    /**
     * Accessor for human-readable file size.
     *
     * @return string
     */
    public function getReadableFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}
