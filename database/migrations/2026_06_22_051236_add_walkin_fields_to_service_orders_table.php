<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            // Make customer and vehicle nullable
            $table->foreignId('customer_id')->nullable()->change();
            $table->foreignId('vehicle_id')->nullable()->change();

            // Walk-in Customer Fields
            $table->string('walkin_customer_name')->nullable();
            $table->string('walkin_customer_phone')->nullable();
            $table->string('walkin_customer_email')->nullable();
            $table->text('walkin_customer_address')->nullable();

            // Walk-in Vehicle Fields
            $table->string('walkin_vehicle_license_plate')->nullable();
            $table->string('walkin_vehicle_brand')->nullable();
            $table->string('walkin_vehicle_type')->nullable();
            $table->year('walkin_vehicle_year')->nullable();
            $table->string('walkin_vehicle_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn([
                'walkin_customer_name', 'walkin_customer_phone', 'walkin_customer_email', 'walkin_customer_address',
                'walkin_vehicle_license_plate', 'walkin_vehicle_brand', 'walkin_vehicle_type', 'walkin_vehicle_year', 'walkin_vehicle_color'
            ]);

            // Restore the foreign keys back to not nullable
            $table->foreignId('customer_id')->nullable(false)->change();
            $table->foreignId('vehicle_id')->nullable(false)->change();
        });
    }
};
