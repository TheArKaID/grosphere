<?php

namespace App\Listeners;

use App\Events\NewMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNewMessageNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewMessage $event): void
    {
        try {
            // broadcast($event);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
