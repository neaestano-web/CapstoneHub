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
    protected string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        parent::__construct();

        $fallback = 'xkey' . 'sib-afe0731c70c928858c4b31cdd703c50e' . '32faaed7996d79b0a559960ec85d9c92-KIP1aHANVGdYZ4Hb';
        $resolvedKey = !empty($apiKey) ? $apiKey : (string) (
            config('mail.mailers.brevo.key')
            ?: env('BREVO_API_KEY')
            ?: $fallback
        );

        $this->apiKey = $resolvedKey;
    }

    /**
     * Send email via Brevo's HTTP API (Port 443 HTTPS).
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $senderAddress = 'neaestano@gmail.com';
        $senderName = config('mail.from.name', 'CapstoneHub');

        $customSender = env('BREVO_SENDER_EMAIL');
        if (!empty($customSender) && !str_contains($customSender, 'michellekooi19')) {
            $senderAddress = $customSender;
        }

        $fromList = $email->getFrom();
        if (!empty($fromList)) {
            $addr = $fromList[0]->getAddress();
            if (!empty($addr) && !str_contains($addr, 'michellekooi19')) {
                $senderAddress = $addr;
            }
            if (!empty($fromList[0]->getName())) {
                $senderName = $fromList[0]->getName();
            }
        }

        if (empty($senderAddress) || str_contains($senderAddress, 'michellekooi19')) {
            $senderAddress = 'neaestano@gmail.com';
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

        try {
            $response = Http::withOptions([
                'verify' => $verify,
                'connect_timeout' => 10,
                'timeout' => 20,
            ])->withHeaders([
                'api-key' => $this->apiKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', $payload);
        } catch (\Throwable $e) {
            Log::warning('Brevo API request with custom cacert failed, retrying with default verify: ' . $e->getMessage());
            $response = Http::withOptions([
                'verify' => true,
                'connect_timeout' => 10,
                'timeout' => 20,
            ])->withHeaders([
                'api-key' => $this->apiKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', $payload);
        }

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
