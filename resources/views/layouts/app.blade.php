<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MotoCare') - Sistem Informasi Bengkel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
@php
    $navLink = 'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition';
    $navIdle = 'text-gray-400 hover:text-gray-100 hover:bg-gray-800';
    $navActive = 'bg-gray-800 text-white border border-gray-700';
@endphp

<div class="min-h-screen lg:flex">
    <aside class="lg:fixed lg:inset-y-0 lg:left-0 lg:w-72 bg-gray-950/95 border-r border-gray-800">
        <div class="h-full flex flex-col">
            <div class="px-5 py-5 border-b border-gray-800">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold bg-gradient-to-r from-orange-300 to-red-400 bg-clip-text text-transparent">MotoCare</p>
                        <p class="text-xs text-gray-500">Sistem Informasi Bengkel</p>
                    </div>
                </a>
            </div>

            @auth
            <div class="px-5 py-4 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-100 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->roles->pluck('name')->join(', ') ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-5 space-y-6 overflow-y-auto">
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Utama</p>
                    <a href="{{ route('dashboard') }}" class="{{ $navLink }} {{ request()->routeIs('dashboard') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                        Dashboard
                    </a>
                </div>

                @can('manage-own-vehicles')
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Pelanggan</p>
                    <a href="{{ route('my-vehicles.index') }}" class="{{ $navLink }} {{ request()->routeIs('my-vehicles.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        Kendaraan Saya
                    </a>
                    <a href="{{ route('booking.create') }}" class="{{ $navLink }} {{ request()->routeIs('booking.create') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                        Booking Servis
                    </a>
                    <a href="{{ route('booking.index') }}" class="{{ $navLink }} {{ request()->routeIs('booking.index', 'booking.show') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-green-400"></span>
                        Riwayat Servis
                    </a>
                </div>
                @endcan

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('service_advisor'))
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Operasional</p>
                    @can('view-bookings')
                    <a href="{{ route('bookings.index') }}" class="{{ $navLink }} {{ request()->routeIs('bookings.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        Booking Pelanggan
                    </a>
                    @endcan
                    @can('manage-orders')
                    <a href="{{ route('service-orders.index') }}" class="{{ $navLink }} {{ request()->routeIs('service-orders.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                        Antrean & Order
                    </a>
                    @endcan
                    @can('view-mechanic-jobs')
                    <a href="{{ route('mechanic-jobs.index') }}" class="{{ $navLink }} {{ request()->routeIs('mechanic-jobs.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                        Monitoring Mekanik
                    </a>
                    @endcan
                </div>
                @endif

                @can('view-mechanic-jobs')
                    @if(auth()->user()->hasRole('mekanik'))
                    <div class="space-y-1">
                        <p class="px-3 text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Mekanik</p>
                        <a href="{{ route('mechanic-jobs.index') }}" class="{{ $navLink }} {{ request()->routeIs('mechanic-jobs.*') ? $navActive : $navIdle }}">
                            <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                            Pekerjaan Saya
                        </a>
                    </div>
                    @endif
                @endcan

                @if(auth()->user()->can('process-payment') || auth()->user()->can('cancel-payment'))
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Pembayaran</p>
                    <a href="{{ route('payments.index') }}" class="{{ $navLink }} {{ request()->routeIs('payments.*') || request()->routeIs('invoices.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-green-400"></span>
                        Pembayaran
                    </a>
                </div>
                @endif

                @if(auth()->user()->can('view-reports') || auth()->user()->can('view-audit-logs') || auth()->user()->can('manage-settings'))
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Manajemen</p>
                    @can('view-reports')
                    <a href="{{ route('reports.index') }}" class="{{ $navLink }} {{ request()->routeIs('reports.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-green-400"></span>
                        Laporan
                    </a>
                    @endcan
                    @can('view-audit-logs')
                    <a href="{{ route('audit-logs.index') }}" class="{{ $navLink }} {{ request()->routeIs('audit-logs.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                        Audit Log
                    </a>
                    @endcan
                    @can('manage-settings')
                    <a href="{{ route('settings.edit') }}" class="{{ $navLink }} {{ request()->routeIs('settings.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                        Pengaturan Bengkel
                    </a>
                    @endcan
                </div>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('service_advisor'))
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Master Data</p>
                    @can('manage-customers')
                    <a href="{{ route('customers.index') }}" class="{{ $navLink }} {{ request()->routeIs('customers.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        Pelanggan
                    </a>
                    @endcan
                    @can('manage-vehicles')
                    <a href="{{ route('vehicles.index') }}" class="{{ $navLink }} {{ request()->routeIs('vehicles.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        Kendaraan
                    </a>
                    @endcan
                    @can('manage-services')
                    <a href="{{ route('services.index') }}" class="{{ $navLink }} {{ request()->routeIs('services.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                        Layanan
                    </a>
                    <a href="{{ route('spareparts.index') }}" class="{{ $navLink }} {{ request()->routeIs('spareparts.*') ? $navActive : $navIdle }}">
                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                        Sparepart
                    </a>
                    @endcan
                </div>
                @endif
            </nav>

            <div class="px-4 py-4 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm text-gray-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition">
                        Logout
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </aside>

    <div class="lg:pl-72 min-h-screen flex flex-col w-full">
        <header class="lg:hidden sticky top-0 z-40 bg-gray-950/95 border-b border-gray-800 px-4 py-3">
            <div class="flex items-center justify-between">
                <span class="font-bold text-orange-400">MotoCare</span>
                <span class="text-xs text-gray-500">{{ auth()->user()->roles->pluck('name')->first() ?? '-' }}</span>
            </div>
        </header>

        @if(session('success'))
        <div class="px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl flex items-center gap-3">
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>

        <footer class="border-t border-gray-800 py-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} MotoCare. Sistem Informasi Bengkel Motor.</p>
                <p class="text-xs text-gray-600">Powered by Laravel 12 & Tailwind CSS 4</p>
            </div>
        </footer>
    </div>
</div>
@include('components.ui.confirm-modal')
</body>
</html>
