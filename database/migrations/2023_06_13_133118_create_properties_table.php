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
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('description');
            $table->decimal('price_usd', 10, 2);
            $table->decimal('eth_price', 10, 2);

            // TODO: feature
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('floors')->nullable();
            $table->integer('area')->nullable();

            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('country');

            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
