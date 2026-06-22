<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'preview_url' => $this->preview_url,
            'full_url' => $this->full_url,
            'alt_text' => $this->alt_text,
            'order' => $this->order,
        ];
    }
}
