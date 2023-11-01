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
        Schema::create('sale_groceries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')->constrained();
            $table->foreignId('grocery_id')->constrained();
            $table->double('quantity');
            $table->double('unit_price');
            $table->double('total');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_groceries');
    }
};
