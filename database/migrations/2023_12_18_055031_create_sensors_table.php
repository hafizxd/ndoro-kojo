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
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained(table: 'kandang');
            $table->string('sensor_latitude')->nullable();
            $table->string('sensor_longitude')->nullable();
            $table->string('sensor_batterypercent')->nullable();
            $table->string('sensor_gps_type')->nullable();
            $table->string('sensor_report')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
