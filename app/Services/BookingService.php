<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Kavling;
use App\Models\BookingItem;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingService
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository
    ) {
    }

    /**
     * Create a new booking
     */
    public function createBooking(array $data, int $userId): Booking
    {
        $kavling = Kavling::findOrFail($data['kavling_id']);

        // Calculate total price
        $checkIn = \Carbon\Carbon::parse($data['tanggal_check_in']);
        $checkOut = \Carbon\Carbon::parse($data['tanggal_check_out']);
        $nights = $checkIn->diffInDays($checkOut);

        $totalHarga = $kavling->harga_per_malam * $nights;

        // Generate unique booking code
        $code = 'BK-' . date('Y') . str_pad(
            Booking::whereYear('created_at', date('Y'))->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        $booking = $this->bookingRepository->create([
            'code' => $code,
            'user_id' => $userId,
            'kavling_id' => $data['kavling_id'],
            'tanggal_check_in' => $data['tanggal_check_in'],
            'tanggal_check_out' => $data['tanggal_check_out'],
            'total_harga' => $totalHarga,
            'status' => 'pending',
        ]);

        // Create booking items if provided
        if (!empty($data['items'])) {
            $this->createBookingItems($booking, $data['items']);
        }

        return $booking->fresh(['kavling', 'items.peralatan', 'user']);
    }

    /**
     * Create booking items for equipment rental
     */
    protected function createBookingItems(Booking $booking, array $items): void
    {
        foreach ($items as $item) {
            $peralatan = \App\Models\Peralatan::findOrFail($item['peralatan_id']);

            BookingItem::create([
                'booking_id' => $booking->id,
                'peralatan_id' => $peralatan->id,
                'jumlah' => $item['qty'],
                'harga_sewa' => $peralatan->harga_sewa,
                'subtotal' => $peralatan->harga_sewa * $item['qty'],
            ]);

            // Update total harga
            $booking->increment('total_harga', $peralatan->harga_sewa * $item['qty']);
        }
    }

    /**
     * Confirm a booking and generate QR code
     */
    public function confirmBooking(int $bookingId): Booking
    {
        $booking = $this->bookingRepository->findOrFail($bookingId);

        // Generate QR Code
        $qrCodePath = $this->generateQRCode($booking);

        // Update booking status
        $booking->update([
            'status' => 'confirmed',
            'qr_code' => $qrCodePath,
        ]);

        return $booking->fresh();
    }

    /**
     * Reject a booking with reason
     */
    public function rejectBooking(int $bookingId, string $reason): Booking
    {
        $this->bookingRepository->updateStatus($bookingId, 'rejected', $reason);
        return $this->bookingRepository->findOrFail($bookingId);
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(int $bookingId): Booking
    {
        $this->bookingRepository->updateStatus($bookingId, 'cancelled');
        return $this->bookingRepository->findOrFail($bookingId);
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(int $bookingId, $file): Booking
    {
        $path = $file->store('payment-proofs', 'public');

        $booking = $this->bookingRepository->findOrFail($bookingId);
        $booking->update([
            'bukti_pembayaran' => $path,
            'status' => 'waiting_confirmation', // Update status after payment upload
        ]);

        return $booking->fresh();
    }

    /**
     * Generate QR code for confirmed booking
     */
    protected function generateQRCode(Booking $booking): string
    {
        $qrCodeData = json_encode([
            'booking_code' => $booking->code,
            'customer' => $booking->user->name ?? 'Guest',
            'check_in' => $booking->tanggal_check_in->format('Y-m-d'),
            'check_out' => $booking->tanggal_check_out->format('Y-m-d'),
            'verified_at' => now()->toISOString(),
        ]);

        $qrCodePath = 'qrcodes/' . $booking->code . '.svg';
        $qrCodeFullPath = storage_path('app/public/' . $qrCodePath);

        // Ensure directory exists
        if (!file_exists(dirname($qrCodeFullPath))) {
            mkdir(dirname($qrCodeFullPath), 0755, true);
        }

        // Generate and save QR code
        QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrCodeData, $qrCodeFullPath);

        return $qrCodePath;
    }
}
