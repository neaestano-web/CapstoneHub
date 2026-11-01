<?php

namespace App\Mail;

use App\Models\DownloadRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DownloadRequestRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public DownloadRequest $downloadRequest)
    {
        // Ensure relationships are loaded
        $this->downloadRequest->loadMissing(['user', 'research']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $title = $this->downloadRequest->research->title ?? 'Research Manuscript';
        $shortTitle = mb_strimwidth($title, 0, 45, '...');

        return new Envelope(
            subject: "Download Request Status: {$shortTitle}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.download-request-rejected',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
