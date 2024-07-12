<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class MailNewMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Message $m
    ) { }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->m = $this->m->refresh();

        if ($this->m->is_read) {
            return;
        }

        $service = new MailService();
        $service->sendMailWithAttachment([
                [
                    'email' => $this->m->recipientUser->email,
                    'name' => $this->m->recipientUser->first_name . ' ' . $this->m->recipientUser->last_name
                ]
            ],
            'New Message', 
            view('emails.new-message', [
                'sender' => $this->m->sender->first_name . ' ' . $this->m->sender->last_name,
                'subdomain' => $this->formSubdomain($this->m->sender->agency?->subdomain),
                'receiver' => $this->m->recipientUser->first_name . ' ' . $this->m->recipientUser->last_name,
                'message' => $this->m->message,
                'messageTime' => Carbon::parse($this->m->created_at)->format('Y-m-d H:i:s')
            ])->render(), 
            [
                [
                    'content' => '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',
                    'filename' => 'logo-up.png',
                    'type' => 'image/png',
                    'disposition' => 'inline'
                ],
                [
                    'content' => 'iVBORw0KGgoAAAANSUhEUgAAABsAAAAYCAYAAAALQIb7AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAEeSURBVHgBtZaLrcIwDEVvmaAjdARGyAhvg9cRGKEjsAFsABu0G8AGgQnKBsYRqaiifNzIHMlKqjq5tvNpgQhE1LGNbDN9cP0e2nihRSTkAE18FilcEC00cBNRmersdsGzJOp/aEHp9VpjoAFPNAjERlTQRMRcKS3iJX2yPXz/znZ1bdM0L9QSZOfK+if0s4Hd2E5sXU5s2ZU26whx2S3ljoyPqoOsEqNAcMhNcIIQ9jUCsXmXGNxjA7xBJm6mglsbE+qWBcYGBNndwgFtUP9N9yD7XzJifSyjNQYb8MEew7Wi9dHxJbCRaI6owAfu5tzHXqSYoQmVz4iBEm7rm4LPHopi9Zdohdi94HOFFoktvzBAGy94pu9XeqIf/Lq9AWnM0iwmnClHAAAAAElFTkSuQmCC',
                    'filename' => 'logo-down.png',
                    'type' => 'image/png',
                    'disposition' => 'inline'
                ]
            ]
        );
    }

    function formSubdomain(string|null $subdomain) : string
    {
        $domain = env('FE_URL');

        if ($subdomain) {
            $domain = str_replace('://', '://' . $subdomain . '.', $domain);
        }

        return $domain;
    }
}
