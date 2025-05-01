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
            Schema::table('tenant_settings', function (Blueprint $table) {
                $table->string('punong_barangay')->nullable()->after('municipality_type');
            });
            
            // Set default value for existing records
            DB::table('tenant_settings')->update([
                'punong_barangay' => 'HON. HALIM T. DIMACANGAN'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tenant_settings')) {
            Schema::table('tenant_settings', function (Blueprint $table) {
                $table->dropColumn('punong_barangay');
            });
        }
    }
}; 