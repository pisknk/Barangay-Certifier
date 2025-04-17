<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixTenantHosts extends Command
{
    protected $signature = 'tenancy:fix-hosts';
    protected $description = 'Generate instructions to fix hosts file for tenant domains';

    public function handle()
    {
        $domains = DB::table('domains')->get();
        
        if ($domains->isEmpty()) {
            $this->warn('No domains found in the database.');
            return Command::FAILURE;
        }
        
        $this->info('To resolve tenant domains locally, add the following entries to your hosts file:');
        $this->info('/etc/hosts on Linux/macOS or C:\\Windows\\System32\\drivers\\etc\\hosts on Windows');
        $this->newLine();
        
        $hosts = "# BrgyCertify tenant domains\n127.0.0.1 localhost\n";
        
        foreach ($domains as $domain) {
            // Extract domain without port
            $domainParts = explode(':', $domain->domain);
            $domainName = $domainParts[0];
            
            $hosts .= "127.0.0.1 {$domainName}\n";
        }
        
        $this->line($hosts);
        
        $this->newLine();
        $this->info('After updating the hosts file, restart your web server.');
        $this->info('Access your tenant applications using: http://[tenant-domain]:8000');
        
        return Command::SUCCESS;
    }
} 