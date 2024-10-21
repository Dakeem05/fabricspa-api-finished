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
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            // $table->foreignId('description_id')
            //     ->constrained('descriptions')
            //     ->cascadeOnDelete();
            $table->json('description_id');
            $table->json('cart_id');
            // $table->json('features');
            $table->float('price', 12, 2);
            $table->float('slashed_price', 12, 2)->nullable();
            $table->float('amount_received', 12, 2)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('payment_id')->nullable();
            $table->string('pay_ref')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('address')->nullable();
            $table->string('street')->nullable();
            $table->string('lga')->nullable();
            $table->string('state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
