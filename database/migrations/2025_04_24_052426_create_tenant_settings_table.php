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
        Schema::create('tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_user_id');
            $table->string('barangay_logo')->nullable();
            $table->string('municipality_logo')->nullable();
            $table->text('address')->nullable();
            $table->string('paper_size')->default('A4');
            $table->string('watermark')->default('None');
            $table->string('theme')->default('light');
            $table->timestamps();
            
            // Index for faster lookups
            $table->index('tenant_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_settings');
    }
};
