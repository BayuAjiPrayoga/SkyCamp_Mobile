<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peralatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'peralatan'; // Non-standard pluralization

    protected $fillable = [
        'nama',
        'kategori',
        'stok_total',
        'harga_sewa',
        'deskripsi',
        'gambar',
        'kondisi',
    ];

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }
}
