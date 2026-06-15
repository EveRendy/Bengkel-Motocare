<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->dateTime('order_date')->useCurrent();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_advisor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mechanic_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('complaint');
            $table->integer('current_km')->default(0);
            $table->decimal('estimated_cost', 12, 2)->default(0);
            $table->enum('status', ['terdaftar', 'menunggu', 'dikerjakan', 'selesai_servis', 'selesai', 'batal'])->default('terdaftar');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};