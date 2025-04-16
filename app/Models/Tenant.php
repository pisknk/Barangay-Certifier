<?php

// app/Models/Tenant.php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    // Define our custom database column names
    protected $fillable = [
        'id', 'name', 'barangay', 'email', 'subscription_plan', 'password', 'is_active'
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
}
