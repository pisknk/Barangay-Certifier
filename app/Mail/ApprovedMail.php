<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use App\Models\Tenant; // ✅ This is the model you're passing
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The setup token for account activation.
     */
    public $setup_token;
    
    /**
     * The domain URL for the tenant.
     */
    public $domain_url;

    /**
     * Create a new message instance.
     */
    public function __construct(public Tenant $tenant, $temp_password = null, $domain_url = null, $setup_token = null)
    {
        // Store the setup token
        $this->setup_token = $setup_token;
        
        // For backwards compatibility
        if ($temp_password) {
            $this->tenant->plain_password = $temp_password;
        }
        
        // Enhanced logging for setup token debugging
        if ($setup_token) {
            Log::info("ApprovedMail: Setup token created for tenant {$tenant->id}", [
                'token_length' => strlen($setup_token), 
                'token_first_chars' => substr($setup_token, 0, 10) . '...',
                'setup_url' => url('/setup-password/' . $tenant->id . '/' . $setup_token),
                'tenant_email' => $tenant->email
            ]);
        } else {
            Log::warning("ApprovedMail: No setup token provided for tenant {$tenant->id}", [
                'tenant_email' => $tenant->email,
                'has_existing_token' => !empty($tenant->setup_token)
            ]);
            
            // If no token was provided but tenant has one, use that
            if (!empty($tenant->setup_token)) {
                $this->setup_token = $tenant->setup_token;
                Log::info("ApprovedMail: Using existing token from tenant record", [
                    'token_length' => strlen($tenant->setup_token),
                    'token_first_chars' => substr($tenant->setup_token, 0, 10) . '...',
                ]);
            }
        }
        
        $this->domain_url = $domain_url;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        Log::info("Building ApprovedMail with setup_token: " . ($this->setup_token ? 'available' : 'not available'));
        return $this->subject('Your Account Has Been Approved!')
            ->view('emails.approved')
            ->with([
                'tenant' => $this->tenant,
                'domain_url' => $this->domain_url,
                'setup_token' => $this->setup_token,
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Account Has Been Approved!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.approved',
            with: [
                'tenant' => $this->tenant,
                'domain_url' => $this->domain_url,
                'setup_token' => $this->setup_token,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
