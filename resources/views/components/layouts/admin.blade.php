<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - LuhurCamp Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Glassmorphism Effects */
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .glass-dark {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Gradient Accent */
        .gradient-primary {
            background: linear-gradient(135deg, #2D5016 0%, #4A7C23 50%, #6B9B3C 100%);
        }

        .gradient-accent {
            background: linear-gradient(135deg, #E87B35 0%, #F59E0B 100%);
        }

        /* Sidebar Styles */
        .sidebar-wrapper {
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 40;
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Smooth Transitions */
        .sidebar-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link:hover {
            transform: translateX(4px);
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(45, 80, 22, 0.1) 0%, transparent 100%);
            border-left: 3px solid #2D5016;
        }

        /* Card Hover Effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar-wrapper {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar-wrapper.open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }
        }

        /* Animation Keyframes */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slideInLeft 0.3s ease forwards;
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
    </style>
</head>

<body class="antialiased bg-slate-50" x-data="{ sidebarOpen: false }">
    <!-- Overlay for mobile -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden"></div>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-admin.sidebar />

        <!-- Main Content -->
        <div class="main-wrapper flex flex-col min-w-0 w-full">
            <!-- Header -->
            <x-admin.header :title="$title ?? 'Dashboard'" />

            <!-- Page Content -->
            <main class="flex-1 p-6 lg:p-8">
                <div class="animate-fade-in">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        function openModal(id) {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: id }));
        }

        function closeModal(id) {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: id }));
        }

        // Sidebar toggle logic remains the same, but removing the manual escape listener 
        // because Alpine handles it in the modal component now.

        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
    </script>

    @stack('scripts')
</body>

</html>