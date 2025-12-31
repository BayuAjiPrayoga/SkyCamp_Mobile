@props(['active' => false])

<aside id="sidebar" class="sidebar-wrapper glass-dark border-r border-slate-700/50" :class="sidebarOpen ? 'open' : ''">
    <div class="h-full flex flex-col">
        <!-- Brand -->
        <div class="flex items-center gap-4 px-6 py-6 border-b border-slate-700/50">
            <div
                class="w-12 h-12 gradient-primary rounded-2xl flex items-center justify-center shadow-lg shadow-green-900/20">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">LuhurCamp</h1>
                <p class="text-xs text-slate-400 font-medium">Admin Dashboard</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 overflow-y-auto" x-data="{ 
            openMaster: {{ request()->routeIs('admin.kavling.*') || request()->routeIs('admin.peralatan.*') ? 'true' : 'false' }},
            openTransaksi: {{ request()->routeIs('admin.booking.*') || request()->routeIs('admin.verifikasi.*') ? 'true' : 'false' }},
            openPengaturan: {{ request()->routeIs('admin.profil') || request()->routeIs('admin.pengumuman.*') ? 'true' : 'false' }} 
        }">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-3">Menu</p>

            <div class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <div
                        class="w-9 h-9 rounded-lg flex items-center justify-center
                        {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-br from-green-500 to-green-600 shadow-lg shadow-green-500/30' : 'bg-slate-700/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                    </div>
                    <span>Dashboard</span>
                </a>

                <!-- Master Data -->
                <div>
                    <button @click="openMaster = !openMaster"
                        class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium
                        {{ request()->routeIs('admin.kavling.*') || request()->routeIs('admin.peralatan.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg flex items-center justify-center
                                {{ request()->routeIs('admin.kavling.*') || request()->routeIs('admin.peralatan.*') ? 'bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-500/30' : 'bg-slate-700/50' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.kavling.*') || request()->routeIs('admin.peralatan.*') ? 'text-white' : 'text-slate-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <span>Master Data</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMaster }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openMaster" x-collapse class="mt-1 ml-6 pl-6 border-l border-slate-700/50 space-y-1">
                        <a href="{{ route('admin.kavling.index') }}"
                            class="block py-2 px-3 text-sm rounded-lg {{ request()->routeIs('admin.kavling.*') ? 'text-green-400 bg-green-500/10' : 'text-slate-400 hover:text-white' }}">
                            Kavling
                        </a>
                        <a href="{{ route('admin.peralatan.index') }}"
                            class="block py-2 px-3 text-sm rounded-lg {{ request()->routeIs('admin.peralatan.*') ? 'text-green-400 bg-green-500/10' : 'text-slate-400 hover:text-white' }}">
                            Peralatan
                        </a>
                    </div>
                </div>

                <!-- Transaksi -->
                <div>
                    <button @click="openTransaksi = !openTransaksi"
                        class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium
                        {{ request()->routeIs('admin.booking.*') || request()->routeIs('admin.verifikasi.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg flex items-center justify-center
                                {{ request()->routeIs('admin.booking.*') || request()->routeIs('admin.verifikasi.*') ? 'bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg shadow-purple-500/30' : 'bg-slate-700/50' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.booking.*') || request()->routeIs('admin.verifikasi.*') ? 'text-white' : 'text-slate-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <span>Transaksi</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openTransaksi }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openTransaksi" x-collapse
                        class="mt-1 ml-6 pl-6 border-l border-slate-700/50 space-y-1">
                        <a href="{{ route('admin.booking.index') }}"
                            class="block py-2 px-3 text-sm rounded-lg {{ request()->routeIs('admin.booking.*') ? 'text-green-400 bg-green-500/10' : 'text-slate-400 hover:text-white' }}">
                            Daftar Booking
                        </a>
                        <a href="{{ route('admin.verifikasi.index') }}"
                            class="flex items-center justify-between py-2 px-3 text-sm rounded-lg {{ request()->routeIs('admin.verifikasi.*') ? 'text-green-400 bg-green-500/10' : 'text-slate-400 hover:text-white' }}">
                            <span>Verifikasi</span>
                            @php $pendingCount = \App\Models\Booking::where('status', 'pending')->whereNotNull('bukti_pembayaran')->count(); @endphp
                            @if($pendingCount > 0)
                                <span
                                    class="px-2 py-0.5 text-xs font-bold bg-orange-500 text-white rounded-full">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Galeri -->
                <a href="{{ route('admin.galeri.index') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.galeri.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <div
                        class="w-9 h-9 rounded-lg flex items-center justify-center
                        {{ request()->routeIs('admin.galeri.*') ? 'bg-gradient-to-br from-pink-500 to-pink-600 shadow-lg shadow-pink-500/30' : 'bg-slate-700/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.galeri.*') ? 'text-white' : 'text-slate-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span>Galeri</span>
                </a>

                <!-- Laporan -->
                <a href="{{ route('admin.laporan.index') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.laporan.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <div
                        class="w-9 h-9 rounded-lg flex items-center justify-center
                        {{ request()->routeIs('admin.laporan.*') ? 'bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg shadow-amber-500/30' : 'bg-slate-700/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.laporan.*') ? 'text-white' : 'text-slate-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span>Laporan</span>
                </a>

                <!-- Pengaturan -->
                <div>
                    <button @click="openPengaturan = !openPengaturan"
                        class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium
                        {{ request()->routeIs('admin.profil') || request()->routeIs('admin.pengumuman.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg flex items-center justify-center
                                {{ request()->routeIs('admin.profil') || request()->routeIs('admin.pengumuman.*') ? 'bg-gradient-to-br from-slate-500 to-slate-600 shadow-lg shadow-slate-500/30' : 'bg-slate-700/50' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.profil') || request()->routeIs('admin.pengumuman.*') ? 'text-white' : 'text-slate-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span>Pengaturan</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openPengaturan }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openPengaturan" x-collapse
                        class="mt-1 ml-6 pl-6 border-l border-slate-700/50 space-y-1">
                        <a href="{{ route('admin.profil') }}"
                            class="block py-2 px-3 text-sm rounded-lg {{ request()->routeIs('admin.profil') ? 'text-green-400 bg-green-500/10' : 'text-slate-400 hover:text-white' }}">
                            Profil
                        </a>
                        <a href="{{ route('admin.pengumuman.index') }}"
                            class="block py-2 px-3 text-sm rounded-lg {{ request()->routeIs('admin.pengumuman.*') ? 'text-green-400 bg-green-500/10' : 'text-slate-400 hover:text-white' }}">
                            Pengumuman
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- User & Logout -->
        <div class="p-4 border-t border-slate-700/50">
            <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-slate-800/50 mb-3">
                <div
                    class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white font-bold shadow-lg">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>