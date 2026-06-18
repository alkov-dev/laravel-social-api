<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


#[OA\Schema(
    schema: "Comment",
    required: ["id", "user_id", "post_id", "content"],
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Уникальный идентификатор комментария",
            example: 1,
            readOnly: true
        ),
        new OA\Property(
            property: "user_id",
            type: "integer",
            description: "ID автора комментария",
            example: 1
        ),
        new OA\Property(
            property: "post_id",
            type: "integer",
            description: "ID поста, к которому относится комментарий",
            example: 1
        ),
        new OA\Property(
            property: "parent_id",
            type: "integer",
            nullable: true,
            description: "ID родительского комментария (null для корневых комментариев)",
            example: null
        ),
        new OA\Property(
            property: "content",
            type: "string",
            description: "Текст комментария",
            minLength: 1,
            maxLength: 1000,
            example: "Отличная статья! Спасибо за полезную информацию."
        ),
        new OA\Property(
            property: "likes_count",
            type: "integer",
            description: "Количество лайков комментария",
            example: 5,
            readOnly: true,
            default: 0
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата создания комментария",
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

class Comment extends Model
{
    protected $fillable = [
        'user_id', 'post_id', 'parent_id', 'content', 'likes_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Для загрузки вложенных комментариев (до 2 уровней)
    public function nestedReplies()
    {
        return $this->replies()->with('user', 'replies.user');
    }
}
