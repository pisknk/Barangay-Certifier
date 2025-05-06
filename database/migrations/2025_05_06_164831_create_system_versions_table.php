<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version_number')->comment('Semantic version number (e.g., 1.0.0)');
            $table->string('version_name')->nullable()->comment('User-friendly version name');
            $table->text('release_notes')->nullable()->comment('Details about the version changes');
            $table->datetime('release_date')->default(now())->comment('When this version was released');
            $table->boolean('is_critical_update')->default(false)->comment('Whether this update is critical');
            $table->timestamps();
        });

        // Insert initial version record
        DB::table('system_versions')->insert([
            'version_number' => '1.0.0',
            'version_name' => 'Initial Release',
            'release_notes' => 'First stable release of Barangay Certifier system.',
            'release_date' => now(),
            'is_critical_update' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_versions');
    }
};
