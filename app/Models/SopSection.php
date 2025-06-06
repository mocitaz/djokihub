<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SopSection extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'introduction'];

    public function items(): HasMany
    {
        return $this->hasMany(SopItem::class);
    }
}