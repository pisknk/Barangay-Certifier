<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovedMail;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?} {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email functionality by sending an approval email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';
        $tenantId = $this->argument('tenant_id');
        
        try {
            $this->info("Testing email sending to: {$email}");
            
            // Test mail configuration
            $this->info("Mail configuration:");
            $this->info("Driver: " . config('mail.default'));
            $this->info("Host: " . config('mail.mailers.smtp.host'));
            $this->info("Port: " . config('mail.mailers.smtp.port'));
            $this->info("Encryption: " . config('mail.mailers.smtp.encryption'));
            $this->info("From address: " . config('mail.from.address'));
            
            // Find or create test tenant
            if ($tenantId) {
                $tenant = Tenant::find($tenantId);
                if (!$tenant) {
                    $this->error("Tenant not found with ID: {$tenantId}");
                    return 1;
                }
            } else {
                // Create a test tenant object for email testing
                $tenant = new Tenant();
                $tenant->id = 'test-tenant';
                $tenant->name = 'Test Tenant';
                $tenant->barangay = 'Test Barangay';
                $tenant->email = $email;
                $tenant->subscription_plan = 'Basic';
            }
            
            // Generate a test setup token
            $setupToken = Str::random(64);
            
            // Set the token property
            $tenant->setup_token = $setupToken;
            
            // Test domain URL
            $domainUrl = 'http://test-tenant.localhost:8000';
            
            // Send test email
            $this->info("Sending test approval email to {$email} with setup token...");
            
            try {
                Mail::to($email)->send(new ApprovedMail($tenant, null, $domainUrl, $setupToken));
                $this->info("Email sent successfully!");
                $this->info("Setup URL: " . url('/setup-password/' . $tenant->id . '/' . $setupToken));
                return 0;
            } catch (\Exception $e) {
                $this->error("Error sending email: " . $e->getMessage());
                $this->error("Full exception: " . $e->getTraceAsString());
                Log::error("Error in TestEmail command: " . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error in TestEmail command: " . $e->getMessage());
            Log::error("Error in TestEmail command: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
}
