<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoCare - Sistem Informasi Bengkel Motor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-950 text-gray-100">
    <main class="min-h-screen">
        <section class="relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(249,115,22,0.22),transparent_34%),linear-gradient(135deg,#111827_0%,#030712_52%,#1f2937_100%)]"></div>
            <div class="absolute inset-0 opacity-30" style="background-image: linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px); background-size: 42px 42px;"></div>

            <div class="relative min-h-screen max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col">
                <header class="flex items-center justify-between gap-4">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold bg-gradient-to-r from-orange-300 to-red-400 bg-clip-text text-transparent">MotoCare</p>
                            <p class="text-xs text-gray-500">Bengkel Motor Digital</p>
                        </div>
                    </a>

                    <nav class="flex items-center gap-3 text-sm">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-300 hover:text-white">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow">Daftar</a>
                    </nav>
                </header>

                <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center py-12">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold text-orange-400 uppercase tracking-wide">Sistem Informasi Bengkel Motor</p>
                        <h1 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight text-white">
                            MotoCare
                        </h1>
                        <p class="mt-5 text-lg text-gray-300 leading-relaxed">
                            Kelola booking servis, antrean bengkel, pekerjaan mekanik, sparepart, pembayaran, invoice, dan riwayat servis kendaraan dalam satu aplikasi.
                        </p>
                        <div class="mt-8 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl shadow-lg shadow-orange-600/20">
                                Masuk ke Sistem
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="bg-gray-900/80 border border-gray-700/70 rounded-2xl shadow-2xl shadow-black/30 overflow-hidden">
                            <div class="h-12 bg-gray-800/80 border-b border-gray-700 flex items-center gap-2 px-4">
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                <span class="ml-3 text-xs text-gray-500">motocare.local/dashboard</span>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                                        <p class="text-2xl font-bold text-white">24</p>
                                        <p class="text-xs text-gray-500 mt-1">Order Hari Ini</p>
                                    </div>
                                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                                        <p class="text-2xl font-bold text-orange-400">8</p>
                                        <p class="text-xs text-gray-500 mt-1">Dikerjakan</p>
                                    </div>
                                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                                        <p class="text-2xl font-bold text-green-400">16</p>
                                        <p class="text-xs text-gray-500 mt-1">Selesai</p>
                                    </div>
                                </div>
                                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                                    <div class="px-4 py-3 border-b border-gray-700 text-sm font-semibold text-gray-200">Antrean Servis</div>
                                    <div class="divide-y divide-gray-700/60 text-sm">
                                        <div class="px-4 py-3 flex justify-between"><span>Honda Beat - B 1234 XYZ</span><span class="text-yellow-400">Menunggu</span></div>
                                        <div class="px-4 py-3 flex justify-between"><span>Yamaha NMAX - B 8877 CD</span><span class="text-orange-400">Dikerjakan</span></div>
                                        <div class="px-4 py-3 flex justify-between"><span>Suzuki Address - B 9081 QA</span><span class="text-purple-400">Selesai Servis</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
