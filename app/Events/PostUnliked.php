<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostUnliked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Post $post) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("post.{$this->post->id}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'post_id' => $this->post->id,
            'likes_count' => $this->post->likes_count,
        ];
    }
}
