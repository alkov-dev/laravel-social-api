<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[OA\Schema(
    schema: "Post",
    required: ["id", "title", "user_id", "category_id"],
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Уникальный идентификатор поста",
            example: 1,
            readOnly: true
        ),
        new OA\Property(
            property: "title",
            type: "string",
            description: "Заголовок поста",
            minLength: 1,
            maxLength: 255,
            example: "Топ-5 упражнений для пресса"
        ),
        new OA\Property(
            property: "content",
            type: "string",
            nullable: true,
            description: "Содержание поста",
            maxLength: 5000,
            example: "Делюсь своей любимой тренировкой для пресса..."
        ),
        new OA\Property(
            property: "user_id",
            type: "integer",
            description: "ID автора поста",
            example: 1
        ),
        new OA\Property(
            property: "category_id",
            type: "integer",
            description: "ID категории поста",
            example: 1
        ),
        new OA\Property(
            property: "likes_count",
            type: "integer",
            description: "Количество лайков",
            example: 42,
            readOnly: true,
            default: 0
        ),
        new OA\Property(
            property: "comments_count",
            type: "integer",
            description: "Количество комментариев",
            example: 15,
            readOnly: true,
            default: 0
        ),
        new OA\Property(
            property: "published_at",
            type: "string",
            format: "date-time",
            nullable: true,
            description: "Дата публикации поста",
            example: "2026-06-19T10:00:00.000000Z"
        ),
        new OA\Property(
            property: "is_published",
            type: "boolean",
            description: "Опубликован ли пост",
            example: true,
            default: true
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата создания поста",
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

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'content', 'user_id', 'category_id',
        'likes_count', 'comments_count', 'published_at', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    public function firstImage()
    {
        return $this->hasOne(PostImage::class)->orderBy('order');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'post_subscriptions')->withTimestamps();
    }
}
