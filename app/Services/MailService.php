<?php

namespace App\Services;

class MailService 
{
    function __construct(
        protected array $config,
        protected \GuzzleHttp\Client $client
    ) {
        $this->config = config('mail.mailers.mail-blast');
        echo $this->config['host'];
    }

    public function sendMail(array $recipients, string $subject, string $message)
    {
        $response = $this->client->request('POST', $this->config['host'] . '/email/send-mail', [
            'headers' => [
                'client-id' => $this->config['client_id'],
                'secret-key' => $this->config['secret_key'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'to' => $recipients,
                'subject' => $subject,
                'message' => [
                    'type' => 'text/html',
                    'value' => $message,
                ]
            ]),
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Send mail with attachment
     * 
     * @param array $recipients
     * @param string $subject
     * @param string $message
     * @param array $attachments
     * 
     * @return mixed
     */
    public function sendMailWithAttachment(array $recipients, string $subject, string $message, array $attachments)
    {
        $response = $this->client->request('POST', $this->config['host'] . '/email/send-mail', [
            'headers' => [
                'client-id' => $this->config['client_id'],
                'secret-key' => $this->config['secret_key'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'to' => $recipients,
                'subject' => $subject,
                'message' => [
                    'type' => 'text/html',
                    'value' => $message,
                ],
                'attachments' => $attachments
            ]),
        ]);

        return json_decode($response->getBody()->getContents());
    }
}