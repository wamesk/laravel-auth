<?php

namespace Wame\LaravelAuth\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\SerializesModels;

class SocialiteAccountAuthEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public JsonResponse $data;

    public string $signature;

    public function __construct(
        JsonResponse $data,
        string $signature
    ) {
        $this->data = $data;
        $this->signature = $signature;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('socialite-account.'.$this->signature),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'socialite-account.auth';
    }

    public function broadcastWith(): mixed
    {
        return json_decode($this->data->getContent(), true);
    }
}
