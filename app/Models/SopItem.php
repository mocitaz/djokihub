<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SopItem extends Model
{
    use HasFactory;

    protected $fillable = ['sop_section_id', 'title', 'description'];

    public function sopSection(): BelongsTo
    {
        return $this->belongsTo(SopSection::class);
    }
}