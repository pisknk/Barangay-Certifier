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
            if (!Schema::hasColumn('tenant_settings', 'header')) {
                Schema::table('tenant_settings', function (Blueprint $table) {
                    $table->text('header')->nullable();
                });
            }
            
            // Also check if address column exists - if it does, we need to rename it
            if (Schema::hasColumn('tenant_settings', 'address')) {
                // Copy data from address to header before dropping
                $settings = DB::table('tenant_settings')->get();
                foreach ($settings as $setting) {
                    if (isset($setting->address) && !isset($setting->header)) {
                        DB::table('tenant_settings')
                            ->where('id', $setting->id)
                            ->update(['header' => $setting->address]);
                    }
                }
                
                // Now remove the address column
                Schema::table('tenant_settings', function (Blueprint $table) {
                    $table->dropColumn('address');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tenant_settings')) {
            if (Schema::hasColumn('tenant_settings', 'header')) {
                Schema::table('tenant_settings', function (Blueprint $table) {
                    $table->dropColumn('header');
                });
            }
            
            if (!Schema::hasColumn('tenant_settings', 'address')) {
                Schema::table('tenant_settings', function (Blueprint $table) {
                    $table->text('address')->nullable();
                });
            }
        }
    }
}; 