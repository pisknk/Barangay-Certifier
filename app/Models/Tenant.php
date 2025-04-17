<?php

// app/Models/Tenant.php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    // Define our custom database column names
    protected $fillable = [
        'id', 'name', 'barangay', 'email', 'subscription_plan', 'password', 'is_active', 'tenant_db', 'valid_until'
    ];

    // Properties that should be included in JSON data
    public static function getCustomColumns(): array
    {
        return [];
    }

    // Make sure data JSON column is properly managed
    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
        'valid_until' => 'datetime',
    ];

    protected $hidden = ['password'];

    // Default values
    protected $attributes = [
        'is_active' => false,
        'data' => '{}',
    ];
    
    /**
     * Get a new instance of the model.
     */
    public function newInstance($attributes = [], $exists = false)
    {
        // Initialize the data property as an empty array if not already set
        if (!isset($attributes['data'])) {
            $attributes['data'] = [];
        }
        
        return parent::newInstance($attributes, $exists);
    }
    
    /**
     * Get the database name for this tenant
     */
    public function getDatabaseName()
    {
        return $this->tenant_db ?? 'tenant_' . $this->id;
    }
    
    /**
     * Set the database name for this tenant
     */
    public function setDatabaseName($name)
    {
        $this->tenant_db = $name;
        return $this;
    }
    
    /**
     * Custom saving event to ensure is_active flag is properly saved
     */
    protected static function booted()
    {
        parent::booted();
        
        static::saving(function ($tenant) {
            // Always update is_active in both model and direct DB to ensure consistency
            if (isset($tenant->attributes['is_active'])) {
                $isActive = (bool)$tenant->attributes['is_active'];
                
                // Log the action
                Log::info("Setting tenant {$tenant->id} is_active to " . ($isActive ? 'true' : 'false'));
                
                // Ensure the attribute is saved as boolean
                $tenant->attributes['is_active'] = $isActive;
                
                // Update directly in DB (if tenant already exists)
                if ($tenant->exists) {
                    DB::table('tenants')
                        ->where('id', $tenant->id)
                        ->update(['is_active' => $isActive]);
                }
            }
        });
    }

    /**
     * Check if the subscription has expired
     */
    public function isExpired(): bool
    {
        if (!$this->valid_until) {
            return false; // If no expiration date set, assume not expired
        }
        
        return now()->greaterThan($this->valid_until);
    }
    
    /**
     * Extend subscription based on plan
     */
    public function extendSubscription(string $plan = null): self
    {
        $plan = $plan ?? $this->subscription_plan;
        
        // Update the subscription plan if provided
        if ($plan !== $this->subscription_plan) {
            $this->subscription_plan = $plan;
        }
        
        // Calculate months to add based on plan
        $monthsToAdd = match (strtolower($plan)) {
            'basic' => 2,
            'essentials' => 3, 
            'ultimate' => 6,
            default => 1,
        };
        
        // Start from current valid_until date if it exists and is in the future
        // Otherwise start from now
        $startDate = ($this->valid_until && $this->valid_until->greaterThan(now())) 
            ? $this->valid_until 
            : now();
            
        $this->valid_until = $startDate->copy()->addMonths($monthsToAdd);
        $this->save();
        
        return $this;
    }
}
