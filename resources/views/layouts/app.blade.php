<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MotoCare') - Sistem Informasi Bengkel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col">
    {{-- Navbar --}}
    <nav class="bg-gray-800/80 backdrop-blur-md border-b border-gray-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:shadow-orange-500/40 transition-shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">
                            MotoCare
                        </span>
                    </a>
                    @auth
                    <div class="hidden md:flex flex-wrap items-center gap-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Dashboard</a>
                        
                        {{-- Menu Khusus Pelanggan --}}
                        @can('manage-own-vehicles')
                        <a href="{{ route('my-vehicles.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('my-vehicles.*') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Kendaraan Saya</a>
                        <a href="{{ route('booking.create') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('booking.create') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Booking Servis</a>
                        <a href="{{ route('booking.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('booking.index') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Riwayat Servis</a>
                        @endcan
                        {{-- Menu Admin & SA (Jangan tampilkan ke pelanggan) --}}
                        @can('view-bookings')
                        <a href="{{ route('bookings.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('bookings.*') ? 'bg-blue-600/30 text-blue-300' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">
                            Booking Pelanggan
                        </a>
                        @endcan
                        @can('manage-orders')
                        <a href="{{ route('service-orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('service-orders.*') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Antrean & Order</a>
                        @endcan
                        @can('manage-customers')
                        <a href="{{ route('customers.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('customers.*') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Pelanggan</a>
                        @endcan
                        
                        @can('manage-vehicles')
                        <a href="{{ route('vehicles.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('vehicles.index', 'vehicles.create', 'vehicles.edit', 'vehicles.show') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Kendaraan (Admin)</a>
                        @endcan
                        
                        @can('manage-services')
                        <a href="{{ route('services.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('services.*') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Layanan</a>
                        <a href="{{ route('spareparts.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('spareparts.*') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">Sparepart</a>
                        @endcan

                        @can('view-mechanic-jobs')
                        <a href="{{ route('mechanic-jobs.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('mechanic-jobs.*') ? 'bg-gray-700/50 text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/30' }}">
                            {{ auth()->user()->hasRole('mekanik') ? 'Pekerjaan Saya' : 'Monitoring Mekanik' }}
                        </a>
                        @endcan
                    </div>
                    @endauth

                {{-- User Menu --}}
                @auth
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2 text-sm text-gray-400">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                        <span class="px-2 py-0.5 bg-gray-700/50 rounded-full text-xs text-gray-400">
                            {{ auth()->user()->roles->pluck('name')->first() ?? '-' }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800/50 border-t border-gray-700/50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} MotoCare. Sistem Informasi Bengkel Motor.</p>
                <p class="text-xs text-gray-600">Powered by Laravel 12 & Tailwind CSS 4</p>
            </div>
        </div>
    </footer>
</body>
</html>