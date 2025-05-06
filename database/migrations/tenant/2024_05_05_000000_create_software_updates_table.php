<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * run the migrations
     */
    public function up(): void
    {
        Schema::create('software_updates', function (Blueprint $table) {
            $table->id();
            $table->string('from_version');
            $table->string('to_version');
            $table->string('status')->default('pending'); // pending, downloading, extracting, updating, completed, failed
            $table->integer('progress')->default(0);
            $table->text('current_step')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('software_updates');
    }
}; 