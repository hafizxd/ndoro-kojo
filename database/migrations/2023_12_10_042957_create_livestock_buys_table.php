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
        Schema::create('livestock_buys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('livestock_id')->constrained();
            $table->foreignId('seller_id')->constrained( table: 'farmers' );
            $table->foreignId('buyer_id')->nullable()->constrained( table: 'farmers' );
            $table->double('price')->nullable();
            $table->enum('status', ['BELUM TERJUAL', 'SUDAH TERJUAL'])->default('BELUM TERJUAL');
            $table->datetime('deal_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestock_buys');
    }
};
