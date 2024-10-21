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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->json('location'); 
            $table->enum('type', ['Full-time', 'Part-time', 'Internship']); 
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('qualification')->nullable();
            $table->longText('offer')->nullable();
            $table->string('pay');
            $table->boolean('is_open')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
