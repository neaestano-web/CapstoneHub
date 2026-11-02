<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct();
    }

    /**
     * Send email via Brevo's HTTP API (Port 443 HTTPS).
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $senderAddress = config('mail.from.address', 'michellekooi19@gmail.com');
        $senderName = config('mail.from.name', 'CapstoneHub');

        $fromList = $email->getFrom();
        if (!empty($fromList)) {
            $senderAddress = $fromList[0]->getAddress();
            $senderName = $fromList[0]->getName() ?: $senderName;
        }

        $to = [];
        foreach ($email->getTo() as $recipient) {
            $to[] = [
                'email' => $recipient->getAddress(),
                'name'  => $recipient->getName() ?: $recipient->getAddress(),
            ];
        }

        $payload = [
            'sender' => [
                'name' => $senderName,
                'email' => $senderAddress,
            ],
            'to' => $to,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?: nl2br((string) $email->getTextBody()),
        ];

        // Include attachments if any
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $headers = $attachment->getPreparedHeaders();
            $filename = $headers->getHeaderParameter('Content-Disposition', 'filename') ?: 'attachment';
            $attachments[] = [
                'name' => $filename,
                'content' => base64_encode($attachment->getBody()),
            ];
        }
        if (!empty($attachments)) {
            $payload['attachment'] = $attachments;
        }

        $verify = file_exists(base_path('cacert.pem')) ? base_path('cacert.pem') : true;

        $response = Http::withOptions(['verify' => $verify])
            ->withHeaders([
                'api-key' => $this->apiKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->timeout(15)->post('https://api.brevo.com/v3/smtp/email', $payload);

        if (!$response->successful()) {
            Log::error('Brevo API send failed: ' . $response->body());
            throw new \RuntimeException('Brevo API error (' . $response->status() . '): ' . $response->body());
        }

        Log::info('Email sent successfully via Brevo HTTP API to ' . json_encode(array_column($to, 'email')));
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
