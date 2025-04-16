<?php

namespace App\Observers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class TenantObserver
{
    /**
     * Handle the Tenant "creating" event.
     */
    public function creating(Tenant $tenant): void
    {
        // Make sure ID is set outside of data
        if (isset($tenant->data['id'])) {
            $tenant->id = $tenant->data['id'];
            unset($tenant->data['id']);
        }
        
        // Log for debugging
        Log::info("Creating tenant {$tenant->id}, is_active value: " . ($tenant->is_active ? 'true' : 'false'));
    }

    /**
     * Handle the Tenant "updating" event.
     */
    public function updating(Tenant $tenant): void
    {
        // Log for debugging
        Log::info("Updating tenant {$tenant->id}, is_active value: " . ($tenant->is_active ? 'true' : 'false'));
        
        // Ensure is_active is properly handled during updates
        if (isset($tenant->data['is_active'])) {
            $tenant->is_active = (bool)$tenant->data['is_active'];
            unset($tenant->data['is_active']);
        }
    }
    
    /**
     * Handle the Tenant "updated" event.
     */
    public function updated(Tenant $tenant): void
    {
        // Log for debugging
        Log::info("Updated tenant {$tenant->id}, is_active value: " . ($tenant->is_active ? 'true' : 'false'));
    }

    /**
     * Handle the Tenant "saving" event.
     */
    public function saving(Tenant $tenant): void
    {
        // Make sure no data attributes override direct columns
        $this->extractDataToDirectColumns($tenant);
        
        // Log for debugging
        Log::info("Saving tenant {$tenant->id}, is_active value: " . ($tenant->is_active ? 'true' : 'false'));
    }
    
    /**
     * Handle the Tenant "saved" event.
     */
    public function saved(Tenant $tenant): void
    {
        // Log for debugging
        Log::info("Saved tenant {$tenant->id}, is_active value: " . ($tenant->is_active ? 'true' : 'false'));
    }

    /**
     * Extract attributes from data JSON to direct columns
     */
    private function extractDataToDirectColumns(Tenant $tenant): void
    {
        if (empty($tenant->data)) {
            return;
        }

        // List of custom columns we've added to the tenants table
        $customColumns = [
            'name', 'barangay', 'email', 'subscription_plan', 'password', 'is_active'
        ];

        foreach ($customColumns as $column) {
            if (isset($tenant->data[$column])) {
                $tenant->{$column} = $tenant->data[$column];
                unset($tenant->data[$column]);
                
                // Special handling for boolean fields
                if ($column === 'is_active') {
                    $tenant->is_active = (bool)$tenant->is_active;
                }
            }
        }
    }
} 