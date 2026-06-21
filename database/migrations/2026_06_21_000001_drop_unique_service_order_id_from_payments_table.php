<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            // 1. Hapus foreign key bawaan terlebih dahulu
            $table->dropForeign('payments_service_order_id_foreign');
            
            // 2. Baru hapus index unique-nya
            $table->dropUnique('payments_service_order_id_unique');

            // 3. Pasang kembali hubungan foreign key tanpa sifat unique
            // Catatan: Asumsi nama tabel tujuannya adalah 'service_orders'
            $table->foreign('service_order_id')
                  ->references('id')
                  ->on('service_orders')
                  ->onDelete('cascade'); // sesuaikan onDelete jika menggunakan 'restrict' atau 'set null'
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            // 1. Hapus dulu foreign key biasa yang dibuat di fungsi up()
            $table->dropForeign('payments_service_order_id_foreign');
            
            // 2. Kembalikan index unique-nya
            $table->unique('service_order_id');

            // 3. Pasang kembali foreign key dengan kondisi unique yang sudah aktif kembali
            $table->foreign('service_order_id')
                  ->references('id')
                  ->on('service_orders')
                  ->onDelete('cascade');
        });
    }
};