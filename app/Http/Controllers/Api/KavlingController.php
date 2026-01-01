<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kavling;
use Illuminate\Http\Request;

class KavlingController extends Controller
{
    /**
     * List all active kavlings
     */
    /**
     * List all active kavlings
     */
    public function index(Request $request)
    {
        $query = Kavling::where('status', 'aktif')
            ->when($request->kapasitas_min, fn($q, $v) => $q->where('kapasitas', '>=', $v))
            ->when($request->kapasitas_max, fn($q, $v) => $q->where('kapasitas', '<=', $v))
            ->when($request->harga_max, fn($q, $v) => $q->where('harga_per_malam', '<=', $v));

        // Remove hard filter, instead calculate availability
        $kavlings = $query->orderBy('nama')->get();

        // Calculate availability for each kavling if dates specific
        if ($request->check_in && $request->check_out) {
            $kavlings->each(function ($kavling) use ($request) {
                $checkIn = $request->check_in;
                $checkOut = $request->check_out;

                $conflicting = $kavling->bookings()
                    ->whereIn('status', ['pending', 'waiting_confirmation', 'confirmed', 'checked_in'])
                    ->where(function ($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('tanggal_check_in', [$checkIn, $checkOut])
                            ->orWhereBetween('tanggal_check_out', [$checkIn, $checkOut])
                            ->orWhere(function ($overlapQ) use ($checkIn, $checkOut) {
                                $overlapQ->where('tanggal_check_in', '<=', $checkIn)
                                    ->where('tanggal_check_out', '>=', $checkOut);
                            });
                    })
                    ->exists();

                $kavling->setAttribute('is_available', !$conflicting);
            });
        } else {
            // Default availability is true if no dates checked
            $kavlings->each(function ($kavling) {
                $kavling->setAttribute('is_available', true);
            });
        }

        return response()->json([
            'success' => true,
            'data' => $kavlings,
        ]);
    }

    /**
     * Get kavling detail with availability check
     */
    public function show(Request $request, Kavling $kavling)
    {
        // Check availability for specific dates if provided
        $isAvailable = true;
        if ($request->check_in && $request->check_out) {
            $conflicting = $kavling->bookings()
                ->whereIn('status', ['pending', 'waiting_confirmation', 'confirmed', 'checked_in'])
                ->where(function ($q) use ($request) {
                    $q->whereBetween('tanggal_check_in', [$request->check_in, $request->check_out])
                        ->orWhereBetween('tanggal_check_out', [$request->check_in, $request->check_out])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('tanggal_check_in', '<=', $request->check_in)
                                ->where('tanggal_check_out', '>=', $request->check_out);
                        });
                })
                ->exists();

            $isAvailable = !$conflicting;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kavling' => $kavling,
                'is_available' => $isAvailable,
            ],
        ]);
    }
}
