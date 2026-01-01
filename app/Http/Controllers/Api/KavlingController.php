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

        // Filter availability if dates are provided
        if ($request->check_in && $request->check_out) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->whereIn('status', ['pending', 'waiting_confirmation', 'confirmed', 'checked_in'])
                    ->where(function ($dateQ) use ($request) {
                        $dateQ->whereBetween('tanggal_check_in', [$request->check_in, $request->check_out])
                            ->orWhereBetween('tanggal_check_out', [$request->check_in, $request->check_out])
                            ->orWhere(function ($overlapQ) use ($request) {
                                $overlapQ->where('tanggal_check_in', '<=', $request->check_in)
                                    ->where('tanggal_check_out', '>=', $request->check_out);
                            });
                    });
            });
        }

        $kavlings = $query->orderBy('nama')->get();

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
