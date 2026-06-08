<?php

namespace App\Mail;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class MailjetApiTransport extends AbstractTransport
{
    protected Client $client;

    public function __construct(string $apiKey, string $secretKey)
    {
        $this->client = new Client($apiKey, $secretKey, true, ['version' => 'v3.1']);
        parent::__construct();
    }

    public function __toString(): string
    {
        return 'mailjet-api';
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (!$email instanceof Email) {
            return;
        }

        $from = $email->getFrom();
        $fromEmail = reset($from);

        $recipients = [];
        foreach ($email->getTo() as $recipient) {
            $recipients[] = [
                'Email' => $recipient->getAddress(),
                'Name' => $recipient->getName() ?? '',
            ];
        }

        $cc = [];
        foreach ($email->getCc() as $recipient) {
            $cc[] = [
                'Email' => $recipient->getAddress(),
                'Name' => $recipient->getName() ?? '',
            ];
        }

        $bcc = [];
        foreach ($email->getBcc() as $recipient) {
            $bcc[] = [
                'Email' => $recipient->getAddress(),
                'Name' => $recipient->getName() ?? '',
            ];
        }

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $fromEmail->getAddress(),
                        'Name' => $fromEmail->getName() ?? config('mail.from.name'),
                    ],
                    'To' => $recipients,
                    'Subject' => $email->getSubject(),
                    'TextPart' => $email->getTextBody() ?? '',
                    'HTMLPart' => $email->getHtmlBody() ?? '',
                ]
            ]
        ];

        if (!empty($cc)) {
            $body['Messages'][0]['Cc'] = $cc;
        }

        if (!empty($bcc)) {
            $body['Messages'][0]['Bcc'] = $bcc;
        }

        try {
            $response = $this->client->post(Resources::$Email, ['body' => $body]);

            if (!$response->success()) {
                throw new \Exception('Mailjet API error: ' . json_encode($response->getData()));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to send email via Mailjet: ' . $e->getMessage(), 0, $e);
        }
    }
}
