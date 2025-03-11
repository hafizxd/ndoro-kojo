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
        Schema::create('livestocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('kandang_id')->constrained(table: 'kandang');
            $table->foreignId('pakan_id')->nullable()->constrained(table: 'pakan');
            $table->foreignId('limbah_id')->nullable()->constrained(table: 'limbah');
            $table->string('age');
            $table->string('gender')->nullable();
            $table->foreignId('type_id')->nullable()->constrained( table: 'livestock_types' );
            $table->enum('acquired_status', ['INPUT', 'BELI', 'LAHIR', 'JUAL', 'BANTUAN PEMERINTAH'])->nullable()->default('INPUT');
            $table->string('acquired_year')->nullable();
            $table->string('acquired_month')->nullable();
            $table->string('acquired_month_name')->nullable();
            $table->string('dead_type')->nullable();
            $table->string('dead_reason')->nullable();
            $table->string('dead_year')->nullable();
            $table->string('dead_month')->nullable();
            $table->string('dead_month_name')->nullable();
            $table->double('sold_proposed_price')->nullable();
            $table->double('sold_deal_price')->nullable();
            $table->string('sold_year')->nullable();
            $table->string('sold_month')->nullable();
            $table->string('sold_month_name')->nullable();
            $table->string('availability')->nullable();
            $table->integer('nominal')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestocks');
    }
};
