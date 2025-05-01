<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDomains extends Command
{
    protected $signature = 'tenancy:check-domains';
    protected $description = 'Check all registered domains and tenants';

    public function handle()
    {
        $this->info('Checking all domains:');
        $domains = DB::table('domains')->get();
        
        if ($domains->isEmpty()) {
            $this->warn('No domains found in the database.');
        } else {
            $this->table(
                ['ID', 'Domain', 'Tenant ID', 'Created At'],
                $domains->map(function ($domain) {
                    return [
                        'id' => $domain->id,
                        'domain' => $domain->domain,
                        'tenant_id' => $domain->tenant_id,
                        'created_at' => $domain->created_at,
                    ];
                })
            );
        }
        
        $this->info('Checking all tenants:');
        $tenants = DB::table('tenants')->get();
        
        if ($tenants->isEmpty()) {
            $this->warn('No tenants found in the database.');
        } else {
            $this->table(
                ['ID', 'Name', 'Barangay', 'Is Active', 'Created At'],
                $tenants->map(function ($tenant) {
                    return [
                        'id' => $tenant->id,
                        'name' => $tenant->name ?? 'N/A',
                        'barangay' => $tenant->barangay ?? 'N/A',
                        'is_active' => isset($tenant->is_active) ? ($tenant->is_active ? 'Yes' : 'No') : 'N/A',
                        'created_at' => $tenant->created_at,
                    ];
                })
            );
        }

        return Command::SUCCESS;
    }
} 