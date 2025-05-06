<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        $version = self::current();
        return $version ? $version->version_number : '1.0.0';
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