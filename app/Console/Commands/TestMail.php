<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\ApprovedMail;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email} {type=welcome}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->argument('type');
        
        $this->info("Sending {$type} email to {$email}...");
        
        try {
            // Create a dummy tenant
            $tenant = new Tenant();
            $tenant->id = 'test-tenant';
            $tenant->name = 'Test Tenant';
            $tenant->barangay = 'Test Barangay';
            $tenant->email = $email;
            $tenant->subscription_plan = 'Basic';
            
            if ($type === 'welcome') {
                Mail::to($email)->send(new WelcomeMail($tenant));
                $this->info('Welcome email sent successfully!');
            } else if ($type === 'approved') {
                $tempPassword = 'test-password-123';
                $domainUrl = 'http://test-tenant.localhost:8000';
                Mail::to($email)->send(new ApprovedMail($tenant, $tempPassword, $domainUrl));
                $this->info('Approval email sent successfully!');
            } else {
                $this->error('Invalid email type. Use welcome or approved.');
                return 1;
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            Log::error('TestMail error: ' . $e->getMessage());
            return 1;
        }
    }
} 