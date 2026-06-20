<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Category",
    required: ["id", "name", "slug", "color"],
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Уникальный идентификатор категории",
            example: 1,
            readOnly: true
        ),
        new OA\Property(
            property: "name",
            type: "string",
            description: "Название категории",
            minLength: 1,
            maxLength: 255,
            example: "Фитнес"
        ),
        new OA\Property(
            property: "slug",
            type: "string",
            description: "URL-идентификатор категории (уникальный)",
            example: "fitness",
            readOnly: true
        ),
        new OA\Property(
            property: "description",
            type: "string",
            nullable: true,
            description: "Описание категории",
            maxLength: 1000,
            example: "Тренировки, спорт, здоровый образ жизни"
        ),
        new OA\Property(
            property: "color",
            type: "string",
            description: "HEX цвет категории (например, #EF4444)",
            pattern: "^#[0-9A-Fa-f]{6}$",
            example: "#EF4444"
        ),
        new OA\Property(
            property: "icon",
            type: "string",
            nullable: true,
            description: "Иконка категории (emoji или SVG)",
            example: "💪"
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата создания категории",
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
class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
    ];


    /**
     * Посты в этой категории
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
