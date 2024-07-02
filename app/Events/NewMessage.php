<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        protected Message $message,
        protected string $recipientType
    ) { }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn(): array
    {
        $channels = [];
        if ($this->recipientType === 'group') {
            foreach ($this->message->getRecipient()->students as $student) {
                $channels[] = new PrivateChannel('App.Models.User.' . $student->user_id);
            }
        }
        else if ($this->recipientType === 'user') {
            $channels = [
                new PrivateChannel('App.Models.User.' . $this->message->recipient_id)
            ];
        }
        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.new';
    }

    /**
     * Broadcast data
     */
    public function broadcastWith(): array
    {
        return [
            'sender_id' => $this->message->sender->id,
            'name' => $this->message->sender->first_name . ' ' . $this->message->sender->last_name,
            'message' => $this->message->message,
        ];
    }
}
