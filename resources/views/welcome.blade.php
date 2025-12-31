@extends('layouts.app')

@section('title', 'Welcome - LuhurCamp')

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 via-background to-secondary-50">
        <div class="text-center p-8">
            <!-- Logo / Brand -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-primary-600 mb-2">🏕️ LuhurCamp</h1>
                <p class="text-gray-600 text-lg">Smart Camping in the Clouds</p>
            </div>

            <!-- Card -->
            <div class="card max-w-md mx-auto mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Selamat Datang!</h2>
                <p class="text-gray-600 mb-6">
                    Project Laravel Anda sudah siap dengan Blade + Tailwind CSS.
                </p>

                <!-- Sample Button with Tailwind Classes -->
                <button class="btn btn-primary w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Mulai Sekarang
                </button>
            </div>

            <!-- Additional Buttons Demo -->
            <div class="flex flex-wrap gap-3 justify-center">
                <button class="btn btn-secondary">Secondary</button>
                <button class="btn btn-accent">Accent</button>
                <button class="btn btn-outline">Outline</button>
                <button class="btn btn-ghost">Ghost</button>
            </div>

            <!-- Footer Text -->
            <p class="mt-8 text-sm text-gray-500">
                Powered by Laravel + Blade + Tailwind CSS v4
            </p>
        </div>
    </div>
@endsection