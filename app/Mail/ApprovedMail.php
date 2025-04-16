<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use App\Models\Tenant; // ✅ This is the model you're passing
use Illuminate\Queue\SerializesModels;

class ApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The temporary password for the tenant.
     */
    public $temp_password;
    
    /**
     * The domain URL for the tenant.
     */
    public $domain_url;

    /**
     * Create a new message instance.
     */
    public function __construct(public Tenant $tenant, $temp_password = null, $domain_url = null)
    {
        $this->temp_password = $temp_password;
        $this->tenant->temp_password = $temp_password;
        $this->domain_url = $domain_url;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Account Has Been Approved!')
            ->view('emails.approved');
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
                'temp_password' => $this->temp_password,
                'domain_url' => $this->domain_url,
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
