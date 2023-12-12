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
        Schema::create('livestock_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('livestock_type');
            $table->tinyInteger('level')->default(1);
            $table->foreignId('parent_type_id')->nullable()->constrained(table: 'livestock_types', indexName: 'id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestock_types');
    }
};
