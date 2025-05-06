<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class SystemVersion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'version_number',
        'version_name',
        'release_notes',
        'release_date',
        'is_critical_update',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'release_date' => 'datetime',
        'is_critical_update' => 'boolean',
    ];
    
    /**
     * Get the connection to use for the model.
     * This automatically detects whether we're in a tenant context or not.
     *
     * @return string
     */
    public function getConnectionName()
    {
        // If app is running in tenant context and 'tenant.id' is set
        if (App::has('tenant') && tenant() && tenant()->id) {
            return 'tenant';
        }
        
        // Otherwise use the default connection
        return parent::getConnectionName();
    }

    /**
     * Get the current system version
     *
     * @return \App\Models\SystemVersion|null
     */
    public static function current()
    {
        return self::latest('id')->first();
    }

    /**
     * Get the current system version number
     *
     * @return string
     */
    public static function currentVersion()
    {
        try {
            $version = self::current();
            return $version ? $version->version_number : '1.0.0';
        } catch (\Exception $e) {
            // If there's an error (like table doesn't exist), return a default version
            return '2.2';
        }
    }

    /**
     * Check if there is a newer version available
     *
     * @param string $currentVersion
     * @return boolean
     */
    public static function hasUpdate($currentVersion)
    {
        $latestVersion = self::currentVersion();
        return version_compare($latestVersion, $currentVersion, '>');
    }
} 