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

        // Get the current version from the central database
        try {
            // Try to get the current version from the central database
            $lastVersion = DB::connection('mysql')->table('system_versions')
                ->orderBy('id', 'desc')
                ->first();
                
            $version = [
                'version_number' => $lastVersion ? $lastVersion->version_number : '2.0.0',
                'version_name' => $lastVersion ? $lastVersion->version_name : 'Tenant Migration',
                'release_notes' => $lastVersion ? $lastVersion->release_notes : 'System migration for tenant database.',
                'release_date' => now(),
                'is_critical_update' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } catch (\Exception $e) {
            // If unable to get from central DB, use hardcoded defaults
            $version = [
                'version_number' => '2.2',
                'version_name' => 'Tenant Default',
                'release_notes' => 'Default version for tenant database.',
                'release_date' => now(),
                'is_critical_update' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert initial version record for tenant
        DB::table('system_versions')->insert($version);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_versions');
    }
}; 