<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kavling extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'slug',
        'kapasitas',
        'harga_per_malam',
        'deskripsi',
        'gambar',
        'status',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    protected $appends = ['gambar_url'];

    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return asset('storage/' . $this->gambar);
        }
        return null;
    }
}
