<x-layouts.guest title="Login">
    <!-- Logo & Title -->
    <div class="text-center mb-8">
        <div
            class="inline-flex items-center justify-center w-16 h-16 gradient-primary rounded-2xl mb-4 shadow-lg shadow-green-900/30">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Selamat Datang</h1>
        <p class="text-slate-500 mt-1">Masuk ke LuhurCamp Admin Panel</p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
            <div class="relative">
                <input type="email" name="email"
                    class="input-modern w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 focus:bg-white"
                    placeholder="admin@luhurcamp.com" value="{{ old('email') }}" required>
                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
            <div class="relative">
                <input type="password" name="password"
                    class="input-modern w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 focus:bg-white"
                    placeholder="••••••••" required>
                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500 focus:ring-offset-0">
                <span class="text-sm text-slate-600">Ingat saya</span>
            </label>
            <a href="#" class="text-sm font-medium text-green-600 hover:text-green-500 transition-colors">Lupa
                password?</a>
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="btn-gradient w-full py-4 px-6 text-white font-semibold rounded-xl flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Masuk ke Dashboard
        </button>
    </form>

    <!-- Demo Credentials -->
    <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-xl">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Demo Credentials</p>
        <div class="space-y-1 text-sm">
            <p class="text-slate-600"><span class="font-medium text-slate-700">Email:</span> admin@luhurcamp.com</p>
            <p class="text-slate-600"><span class="font-medium text-slate-700">Password:</span> password123</p>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-center text-slate-400 text-sm mt-6">
        © {{ date('Y') }} LuhurCamp. All rights reserved.
    </p>
</x-layouts.guest>