<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class WeatherService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openweathermap.org/data/2.5/weather';

    // Gunung Luhur coordinates (from: https://maps.app.goo.gl/kxvHhMJSeciYF26y9)
    // Bumi Perkemahan Gunung Luhur, Lebak, Banten
    private float $lat = -6.7318;
    private float $lon = 106.4572;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key', '');
    }

    /**
     * Get current weather data with 30-minute cache
     *
     * @return array<string, mixed>|null
     */
    public function getCurrentWeather(): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $cacheKey = 'weather_data_luhurcamp';

        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            try {
                /** @var Response $response */
                $response = Http::timeout(5)->get($this->baseUrl, [
                    'lat' => $this->lat,
                    'lon' => $this->lon,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'id',
                ]);

                if ($response->successful()) {
                    /** @var array<string, mixed> $data */
                    $data = $response->json();

                    return [
                        'temp' => round($data['main']['temp'] ?? 21),
                        'description' => ucfirst($data['weather'][0]['description'] ?? 'Cerah'),
                        'humidity' => $data['main']['humidity'] ?? 65,
                        'wind' => round(($data['wind']['speed'] ?? 3.3) * 3.6), // Convert m/s to km/h
                        'icon' => $data['weather'][0]['icon'] ?? '01d',
                        'feels_like' => round($data['main']['feels_like'] ?? 21),
                    ];
                }

                Log::warning('OpenWeather API returned non-successful response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('OpenWeather API error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get weather icon URL
     */
    public static function getIconUrl(string $iconCode): string
    {
        return "https://openweathermap.org/img/wn/{$iconCode}@2x.png";
    }
}
