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
        Schema::create('farmers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('fullname');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('occupation', ['PETERNAK', 'PEDAGANG TERNAK'])->nullable();
            $table->enum('gender', ['LAKI-LAKI', 'PEREMPUAN'])->nullable();
            $table->integer('age')->nullable();
            $table->string('kelompok_ternak')->nullable();
            $table->foreignId('province_id')->nullable()->constrained();
            $table->foreignId('regency_id')->nullable()->constrained();
            $table->foreignId('district_id')->nullable()->constrained();
            $table->string('village_id')->nullable();
            $table->foreign('village_id')->references('id')->on('villages')->constrained();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
