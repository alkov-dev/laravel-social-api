<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

#[OA\Schema(
    schema: "PostImage",
    required: ["id", "preview_url", "full_url"],
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Уникальный идентификатор изображения",
            example: 1,
            readOnly: true
        ),
        new OA\Property(
            property: "post_id",
            type: "integer",
            description: "ID поста, к которому принадлежит изображение",
            example: 1
        ),
        new OA\Property(
            property: "preview_url",
            type: "string",
            format: "uri",
            description: "URL превью изображения (оптимизированное, ~400x300)",
            example: "https://example.com/storage/posts/previews/abc123.jpg"
        ),
        new OA\Property(
            property: "full_url",
            type: "string",
            format: "uri",
            description: "URL полного изображения (оригинальный размер)",
            example: "https://example.com/storage/posts/full/abc123.jpg"
        ),
        new OA\Property(
            property: "alt_text",
            type: "string",
            nullable: true,
            description: "Альтернативный текст для изображения (для SEO и доступности)",
            maxLength: 255,
            example: "Упражнения для пресса"
        ),
        new OA\Property(
            property: "order",
            type: "integer",
            description: "Порядок отображения изображений в посте",
            example: 0,
            default: 0,
            minimum: 0
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата загрузки изображения",
            example: "2026-06-19T10:00:00.000000Z",
            readOnly: true
        ),
        new OA\Property(
            property: "updated_at",
            type: "string",
            format: "date-time",
            description: "Дата последнего обновления",
            example: "2026-06-19T10:00:00.000000Z",
            readOnly: true
        ),
    ]
)]

class PostImage extends Model
{
    protected $fillable = [
        'post_id', 'preview_url', 'full_url', 'alt_text', 'order',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
