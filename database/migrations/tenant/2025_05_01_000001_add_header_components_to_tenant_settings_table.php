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
        if (Schema::hasTable('tenant_settings')) {
            // Add new columns for header components
            Schema::table('tenant_settings', function (Blueprint $table) {
                $table->string('province')->nullable()->after('header');
                $table->string('municipality')->nullable()->after('province');
                $table->enum('municipality_type', ['Municipality', 'City'])->default('Municipality')->after('municipality');
            });
            
            // Get all tenant settings and extract header components
            $settings = DB::table('tenant_settings')->get();
            foreach ($settings as $setting) {
                if (isset($setting->header)) {
                    // Try to parse the header to extract province, municipality, etc.
                    $lines = explode("\n", $setting->header);
                    
                    $province = null;
                    $municipality = null;
                    $municipalityType = 'Municipality';
                    
                    foreach ($lines as $line) {
                        if (strpos(strtolower($line), 'province of') !== false) {
                            $province = trim(str_replace('Province of', '', $line));
                        } else if (strpos(strtolower($line), 'municipality of') !== false) {
                            $municipality = trim(str_replace('Municipality of', '', $line));
                            $municipalityType = 'Municipality';
                        } else if (strpos(strtolower($line), 'city of') !== false) {
                            $municipality = trim(str_replace('City of', '', $line));
                            $municipalityType = 'City';
                        }
                    }
                    
                    // Update the record with extracted values
                    DB::table('tenant_settings')
                        ->where('id', $setting->id)
                        ->update([
                            'province' => $province,
                            'municipality' => $municipality,
                            'municipality_type' => $municipalityType,
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tenant_settings')) {
            Schema::table('tenant_settings', function (Blueprint $table) {
                $table->dropColumn('province');
                $table->dropColumn('municipality');
                $table->dropColumn('municipality_type');
            });
        }
    }
}; 