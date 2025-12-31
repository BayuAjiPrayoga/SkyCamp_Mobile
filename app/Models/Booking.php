<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon|null $tanggal_check_in
 * @property \Illuminate\Support\Carbon|null $tanggal_check_out
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'kavling_id',
        'tanggal_check_in',
        'tanggal_check_out',
        'total_harga',
        'status',
        'bukti_pembayaran',
        'rejection_reason',
        'qr_code',
    ];

    protected $casts = [
        'tanggal_check_in' => 'date',
        'tanggal_check_out' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kavling()
    {
        return $this->belongsTo(Kavling::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }
}
