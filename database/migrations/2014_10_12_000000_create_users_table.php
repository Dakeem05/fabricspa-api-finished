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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('google_id')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('picture')->nullable();
            $table->string('dob')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->float('cart_total', 14, 2)->nullable();
            $table->float('discounted_cart_total', 14, 2)->nullable();
            $table->enum('role', ['client', 'admin'])->default('client');
            $table->json('promo_codes')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
