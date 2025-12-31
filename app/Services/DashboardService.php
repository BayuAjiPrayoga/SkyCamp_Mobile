<?php

namespace App\Services;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\KavlingRepositoryInterface;
use App\Repositories\Contracts\PeralatanRepositoryInterface;

class DashboardService
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository,
        protected KavlingRepositoryInterface $kavlingRepository,
        protected PeralatanRepositoryInterface $peralatanRepository,
        protected WeatherService $weatherService
    ) {
    }

    /**
     * Get all dashboard data
     */
    public function getDashboardData(): array
    {
        return [
            'todayBookings' => $this->bookingRepository->getTodayCount(),
            'monthlyRevenue' => $this->bookingRepository->getMonthlyRevenue(now()->month, now()->year),
            'availableKavling' => $this->kavlingRepository->getAvailableCount(),
            'totalKavling' => $this->kavlingRepository->getTotalCount(),
            'availableGear' => $this->peralatanRepository->getAvailableStock(),
            'totalGear' => $this->peralatanRepository->getTotalStock(),
            'pendingBookings' => $this->getPendingBookingsPreview(),
            'pendingCount' => $this->getPendingCount(),
            'weather' => $this->getWeatherData(),
        ];
    }

    /**
     * Get pending bookings for dashboard preview (limited to 5)
     */
    public function getPendingBookingsPreview()
    {
        return $this->bookingRepository
            ->findPendingWithPayment()
            ->take(5);
    }

    /**
     * Get pending verification count
     */
    public function getPendingCount(): int
    {
        return $this->bookingRepository
            ->findPendingWithPayment()
            ->count();
    }

    /**
     * Get weather data with fallback
     */
    public function getWeatherData(): array
    {
        try {
            $apiKey = config('services.openweather.key');
            if ($apiKey) {
                $weather = $this->weatherService->getCurrentWeather();
                if ($weather) {
                    return $weather;
                }
            }
        } catch (\Exception $e) {
            // Fallback to static data
        }

        return [
            'temp' => 21,
            'description' => 'Cerah Berawan',
            'humidity' => 65,
            'wind' => 12,
        ];
    }
}
