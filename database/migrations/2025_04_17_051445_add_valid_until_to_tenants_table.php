<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('valid_until')->nullable()->after('is_active');
        });
        
        // Update existing tenants with expiration dates based on their subscription plan
        $tenants = DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            $monthsToAdd = $this->getMonthsForPlan($tenant->subscription_plan);
            
            // Use created_at as the starting point, or now if it's not available
            $startDate = $tenant->created_at ? Carbon::parse($tenant->created_at) : Carbon::now();
            $validUntil = $startDate->copy()->addMonths($monthsToAdd);
            
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['valid_until' => $validUntil]);
        }
    }

    /**
     * Get months to add based on subscription plan
     */
    private function getMonthsForPlan(string $plan): int
    {
        return match (strtolower($plan)) {
            'basic' => 2,
            'essentials' => 3,
            'ultimate' => 6,
            default => 1, // Default to 1 month for unknown plans
        };
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('valid_until');
        });
    }
};
