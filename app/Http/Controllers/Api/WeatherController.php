<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    /**
     * Get current weather data
     */
    public function current(WeatherService $weatherService)
    {
        $weather = $weatherService->getCurrentWeather();

        if (!$weather) {
            // Return fallback data if API fails
            $weather = [
                'temp' => 21,
                'description' => 'Cerah Berawan',
                'humidity' => 65,
                'wind' => 12,
                'icon' => '02d',
                'feels_like' => 21,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $weather,
            'location' => 'Gunung Luhur, West Java',
        ]);
    }
}
