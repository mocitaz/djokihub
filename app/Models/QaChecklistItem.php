<?php

// File: app/Models/QaChecklistItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaChecklistItem extends Model
{
    use HasFactory;

    // Nama tabel eksplisit jika tidak mengikuti konvensi jamak dari nama model
    // protected $table = 'qa_checklist_items'; 

    protected $fillable = [
        'project_id', 'item_description', 'status', 'notes', 'checked_by_user_id', 'checked_at', 'order',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }
}
