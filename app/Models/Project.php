<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'client_name',
        'order_id',
        'description',
        'start_date',
        'end_date',
        'budget',
        'status',
        'payment_status',
        'created_by_user_id',
        'notes',
        'poc', // Bisa path file atau URL
        'bast', // Bisa path file atau URL
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'float', 
    ];

    // Relasi ke User (staff yang ditugaskan)
    public function staff()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')->withTimestamps();
    }

    // Relasi ke User (pembuat proyek)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Accessor untuk mendapatkan URL file PoC
    public function getFilePocUrlAttribute()
    {
        if ($this->poc) {
            // Cek apakah ini URL absolut
            if (filter_var($this->poc, FILTER_VALIDATE_URL)) {
                return $this->poc;
            }
            // Jika bukan URL, asumsikan ini adalah path di storage
            if (Storage::disk('public')->exists($this->poc)) {
                return Storage::disk('public')->url($this->poc);
            }
        }
        return null; // Atau gambar placeholder jika ada
    }

    // Accessor untuk mendapatkan nama file PoC saja
    public function getPocFilenameAttribute()
    {
        if ($this->poc && !filter_var($this->poc, FILTER_VALIDATE_URL)) {
            return basename($this->poc);
        }
        return null;
    }


    // Accessor untuk mendapatkan URL file BAST
    public function getFileBastUrlAttribute()
    {
        if ($this->bast) {
            if (filter_var($this->bast, FILTER_VALIDATE_URL)) {
                return $this->bast;
            }
            if (Storage::disk('public')->exists($this->bast)) {
                return Storage::disk('public')->url($this->bast);
            }
        }
        return null;
    }

    // Accessor untuk mendapatkan nama file BAST saja
    public function getBastFilenameAttribute()
    {
        if ($this->bast && !filter_var($this->bast, FILTER_VALIDATE_URL)) {
            return basename($this->bast);
        }
        return null;
    }
    
    /**
     * Mendefinisikan relasi one-to-many ke ProjectRequirement.
     */
    public function requirements(): HasMany
    {
        // Pastikan model ProjectRequirement ada dan namespace-nya benar
        return $this->hasMany(ProjectRequirement::class)->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    // Append accessors to model's array form
    protected $appends = ['file_poc_url', 'poc_filename', 'file_bast_url', 'bast_filename'];
}
