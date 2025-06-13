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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['time', 'quantity']);
            $table->integer('min_quantity')->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('days_of_week')->nullable();
            $table->integer('precedence')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
