<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'peralatan_id',
        'jumlah',
        'harga_sewa',
        'subtotal',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }
}
