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
        Schema::table('kandang', function (Blueprint $table) {
            $table->foreignId('type_id')->nullable()->constrained( table: 'livestock_types' );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
