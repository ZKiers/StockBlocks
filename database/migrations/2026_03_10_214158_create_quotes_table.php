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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id');
            $table->float('price');
            $table->float('change')->nullable();
            $table->float('percent_change')->nullable();
            $table->float('daily_high')->nullable();
            $table->float('daily_low')->nullable();
            $table->float('open')->nullable();
            $table->float('previous_close')->nullable();
            $table->timestamp('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
