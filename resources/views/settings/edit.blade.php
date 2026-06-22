@extends('layouts.app')

@section('title', 'Pengaturan Bengkel')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Pengaturan Bengkel</h1>
            <p class="text-gray-400 mt-1">Data ini dipakai untuk identitas invoice dan dokumen bengkel.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="w-fit px-4 py-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-200 rounded-lg text-sm">
            Kembali
        </a>
    </div>

    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6">
        <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Nama Bengkel</label>
                    <input type="text" name="workshop_name" value="{{ old('workshop_name', $settings['workshop_name']) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                    <x-ui.field-error name="workshop_name" />
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Teks Logo Singkat</label>
                    <input type="text" name="workshop_logo_text" value="{{ old('workshop_logo_text', $settings['workshop_logo_text']) }}" maxlength="10" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                    <x-ui.field-error name="workshop_logo_text" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-400 mb-2">Tagline</label>
                    <input type="text" name="workshop_tagline" value="{{ old('workshop_tagline', $settings['workshop_tagline']) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                    <x-ui.field-error name="workshop_tagline" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-400 mb-2">Alamat</label>
                    <textarea name="workshop_address" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">{{ old('workshop_address', $settings['workshop_address']) }}</textarea>
                    <x-ui.field-error name="workshop_address" />
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Telepon</label>
                    <input type="text" name="workshop_phone" value="{{ old('workshop_phone', $settings['workshop_phone']) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                    <x-ui.field-error name="workshop_phone" />
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Email</label>
                    <input type="email" name="workshop_email" value="{{ old('workshop_email', $settings['workshop_email']) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                    <x-ui.field-error name="workshop_email" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-400 mb-2">Jam Operasional</label>
                    <input type="text" name="workshop_operational_hours" value="{{ old('workshop_operational_hours', $settings['workshop_operational_hours']) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                    <x-ui.field-error name="workshop_operational_hours" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-700">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-gray-400 hover:text-gray-200">Batal</a>
                <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
