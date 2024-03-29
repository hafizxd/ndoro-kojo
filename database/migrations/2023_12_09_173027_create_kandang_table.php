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
        Schema::create('kandang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('farmer_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->double('panjang');
            $table->double('lebar');
            $table->double('luas')->nullable();
            $table->enum('jenis', ['KANDANG KECIL', 'KANDANG MEDIUM', 'KANDANG BESAR']);
            $table->foreignId('province_id')->nullable()->constrained();
            $table->foreignId('regency_id')->nullable()->constrained();
            $table->foreignId('district_id')->nullable()->constrained();
            $table->string('village_id')->nullable();
            $table->foreign('village_id')->references('id')->on('villages')->constrained();
            $table->text('address')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kandang');
    }
};
