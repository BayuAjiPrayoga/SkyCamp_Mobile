<x-layouts.admin title="Profil Saya">
    <div class="max-w-2xl">
        <!-- Profile Card -->
        <x-ui.card class="mb-6">
            <div class="flex items-center gap-6 mb-6">
                <div class="w-20 h-20 bg-primary-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ auth()->user()->name ?? 'Admin LuhurCamp' }}</h2>
                    <p class="text-gray-500">{{ auth()->user()->email ?? 'admin@luhurcamp.com' }}</p>
                    <x-ui.badge variant="success" class="mt-2">Administrator</x-ui.badge>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.profil.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <x-ui.input 
                        label="Nama Lengkap" 
                        name="name" 
                        :value="auth()->user()->name ?? 'Admin LuhurCamp'"
                        :error="$errors->first('name')"
                        required 
                    />

                    <x-ui.input 
                        label="Email" 
                        name="email" 
                        type="email"
                        :value="auth()->user()->email ?? 'admin@luhurcamp.com'"
                        :error="$errors->first('email')"
                        required 
                    />

                    <x-ui.input 
                        label="No. Telepon" 
                        name="phone" 
                        type="tel"
                        :value="auth()->user()->phone ?? ''"
                        placeholder="+62 812-xxxx-xxxx"
                    />
                </div>

                <div class="flex justify-end mt-6">
                    <x-ui.button type="submit" variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <!-- Change Password -->
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ubah Password</h3>

            <form method="POST" action="{{ route('admin.profil.password') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <x-ui.input 
                        label="Password Saat Ini" 
                        name="current_password" 
                        type="password"
                        :error="$errors->first('current_password')"
                        required 
                    />

                    <x-ui.input 
                        label="Password Baru" 
                        name="password" 
                        type="password"
                        :error="$errors->first('password')"
                        helper="Minimal 8 karakter"
                        required 
                    />

                    <x-ui.input 
                        label="Konfirmasi Password Baru" 
                        name="password_confirmation" 
                        type="password"
                        required 
                    />
                </div>

                <div class="flex justify-end mt-6">
                    <x-ui.button type="submit" variant="secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Ubah Password
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.admin>
