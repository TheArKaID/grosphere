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
        if (($error = $this->validateMail($recipients, $subject, $message)) !== true) {
            return $error;
        }

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
        if (($error = $this->validateMail($recipients, $subject, $message)) !== true) {
            return $error;
        }

        if (($error = $this->validateAttachments($attachments)) !== true) {
            return $error;
        }

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

    /**
     * Validate $recipients, $subject, and $message
     * 
     * $recipients should be an array of object containing email and name
     * $subject should be a string
     * $message could be a clean string or html string
     * 
     * @param array $recipients
     * @param string $subject
     * @param string $message
     * 
     * @return bool|string
     */
    public function validateMail(array $recipients, $subject, $message)
    {
        if (!is_array($recipients)) {
            return 'Recipient should be an array';
        }

        if (count($recipients) === 0) {
            return 'Recipient should not be empty';
        }

        foreach ($recipients as $recipient) {
            if (!isset($recipient['email']) || !isset($recipient['name'])) {
                return 'Recipient should be an array of object containing email and name';
            }
        }

        if (!is_string($subject)) {
            return 'Subject should be a string';
        }

        if (!is_string($message)) {
            return 'Message should be a string';
        }

        return true;
    }
    
    /**
     * Validate $attachments
     * 
     * $attachments should be an array of object containing content (valid base64 string), filename, type, and disposition
     * 
     * @param array $attachments
     * 
     * @return bool|string
     */
    public function validateAttachments(array $attachments)
    {
        if (!is_array($attachments)) {
            return 'Attachments should be an array';
        }

        if (count($attachments) === 0) {
            return 'Attachments should not be empty';
        }
        

        foreach ($attachments as $attachment) {
            if (!isset($attachment['content']) || !isset($attachment['filename']) || !isset($attachment['type']) || !isset($attachment['disposition'])) {
                return 'Attachment should be an array of object containing content, filename, type, and disposition';
            }

            if ($attachment['disposition'] !== 'attachment' && $attachment['disposition'] !== 'inline') {
                return "Attachment's 'disposition' should be either 'attachment' or 'inline'";
            }

            // Validate base64 string
            if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $attachment['content'])) {
                return 'Attachment content should be a valid base64 string';
            }
        }

        return true;
    }
}