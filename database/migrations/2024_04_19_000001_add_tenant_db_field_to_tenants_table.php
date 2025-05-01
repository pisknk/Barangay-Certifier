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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('tenant_db')->nullable()->after('is_active');
        });
        
        // Update existing tenants with their database names
        $tenants = DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['tenant_db' => 'tenant_' . $tenant->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('tenant_db');
        });
    }
}; 