<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} - LuhurCamp</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .login-gradient {
            background: linear-gradient(135deg, #1E3A5F 0%, #2D5016 50%, #4A7C23 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .floating-shapes {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-shapes::before,
        .floating-shapes::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }

        .floating-shapes::before {
            width: 600px;
            height: 600px;
            background: #fff;
            top: -200px;
            right: -200px;
            animation: float 20s infinite ease-in-out;
        }

        .floating-shapes::after {
            width: 400px;
            height: 400px;
            background: #E87B35;
            bottom: -100px;
            left: -100px;
            animation: float 15s infinite ease-in-out reverse;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(20px, 20px) rotate(5deg);
            }

            50% {
                transform: translate(0, 40px) rotate(0deg);
            }

            75% {
                transform: translate(-20px, 20px) rotate(-5deg);
            }
        }

        .input-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-modern:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(45, 80, 22, 0.2);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #2D5016 0%, #4A7C23 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(45, 80, 22, 0.4);
        }

        .btn-gradient:active {
            transform: translateY(0);
        }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen login-gradient relative flex items-center justify-center p-4">
        <!-- Floating Shapes Background -->
        <div class="floating-shapes"></div>

        <!-- Login Card -->
        <div class="glass-card w-full max-w-md rounded-3xl shadow-2xl p-8 relative z-10">
            {{ $slot }}
        </div>

        <!-- Mountain Silhouette -->
        <div class="absolute bottom-0 left-0 right-0 h-32 opacity-20 pointer-events-none">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                class="w-full h-full">
                <path
                    d="M0 120L48 110C96 100 192 80 288 70C384 60 480 60 576 65C672 70 768 80 864 85C960 90 1056 90 1152 85C1248 80 1344 70 1392 65L1440 60V120H1392C1344 120 1248 120 1152 120C1056 120 960 120 864 120C768 120 672 120 576 120C480 120 384 120 288 120C192 120 96 120 48 120H0Z"
                    fill="white" />
            </svg>
        </div>
    </div>
</body>

</html>