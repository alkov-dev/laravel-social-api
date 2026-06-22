<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\Post;
use App\Http\Resources\CommentResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Comment $comment,
        public Post $post
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("post.{$this->post->id}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'comment_added',
            'post_id' => $this->post->id,
            'comment' => new CommentResource($this->comment->load('user')),
            'comments_count' => $this->post->comments_count,
        ];
    }
}
