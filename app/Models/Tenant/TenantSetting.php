<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_settings';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'tenant';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_user_id',
        'barangay_logo',
        'municipality_logo',
        'header',
        'province',
        'municipality',
        'municipality_type',
        'punong_barangay',
        'paper_size',
        'theme',
        'theme_settings',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'theme_settings' => 'array',
    ];
    
    /**
     * Get the user that owns the settings.
     */
    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'tenant_user_id');
    }
    
    /**
     * Get the full header text with proper formatting.
     * 
     * @return string
     */
    public function getFormattedHeaderAttribute()
    {
        $lines = [
            'Republic of the Philippines'
        ];
        
        if (!empty($this->province)) {
            $lines[] = 'Province of ' . $this->province;
        }
        
        if (!empty($this->municipality)) {
            $lines[] = $this->municipality_type . ' of ' . $this->municipality;
        }
        
        // Add a line for the tenant's barangay
        $tenant = tenant();
        if ($tenant && !empty($tenant->barangay)) {
            $lines[] = 'Barangay ' . $tenant->barangay;
        }
        
        return implode("\n", $lines);
    }
}
