<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'published_at' => $this->published_at?->toDateTimeString(),
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'is_liked' => $this->isLikedBy($request->user()),
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'images' => PostImageResource::collection($this->whenLoaded('images')),
            'preview_image' => new PostImageResource($this->whenLoaded('firstImage')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
