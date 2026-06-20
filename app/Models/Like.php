<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Like",
    required: ["id", "user_id", "post_id"],
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Уникальный идентификатор лайка",
            example: 1,
            readOnly: true
        ),
        new OA\Property(
            property: "user_id",
            type: "integer",
            description: "ID пользователя, который поставил лайк",
            example: 1
        ),
        new OA\Property(
            property: "post_id",
            type: "integer",
            description: "ID поста, который лайкнули",
            example: 1
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата и время постановки лайка",
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



class Like extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'post_id' => 'integer',
        ];
    }


    /**
     * Пользователь, который поставил лайк
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Пост, который лайкнули
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }


}
