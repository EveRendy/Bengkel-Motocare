<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan - MotoCare</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-red-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-2xl relative z-10 my-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">MotoCare</h1>
            <p class="text-gray-500 mt-2">Daftar Akun Pelanggan Baru</p>
        </div>

        <div class="bg-gray-800/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl p-8">
            @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-xl text-gray-100 focus:ring-2 focus:ring-orange-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No HP / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-xl text-gray-100 focus:ring-2 focus:ring-orange-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-xl text-gray-100 focus:ring-2 focus:ring-orange-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Alamat Lengkap</label>
                        <input type="text" name="address" value="{{ old('address') }}" required class="w-full px-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-xl text-gray-100 focus:ring-2 focus:ring-orange-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-xl text-gray-100 focus:ring-2 focus:ring-orange-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-xl text-gray-100 focus:ring-2 focus:ring-orange-500/50">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 mt-6 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-semibold rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5">
                    Daftar Sekarang
                </button>
                
                <p class="text-center text-gray-400 mt-4 text-sm">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-orange-400 hover:text-orange-300">Login di sini</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>