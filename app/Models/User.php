<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;


#[OA\Schema(
    schema: "User",
    required: ["id", "name", "email"],
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Уникальный идентификатор пользователя",
            example: 1,
            readOnly: true
        ),
        new OA\Property(
            property: "name",
            type: "string",
            description: "Имя пользователя",
            minLength: 2,
            maxLength: 255,
            example: "Иван Иванов"
        ),
        new OA\Property(
            property: "email",
            type: "string",
            format: "email",
            description: "Email для входа",
            example: "ivan@example.com"
        ),
        new OA\Property(
            property: "avatar",
            type: "string",
            format: "uri",
            nullable: true,
            description: "URL аватарки пользователя",
            example: "https://api.dicebear.com/7.x/avataaars/svg?seed=Ivan"
        ),
        new OA\Property(
            property: "bio",
            type: "string",
            nullable: true,
            description: "О себе",
            maxLength: 500,
            example: "Фитнес-тренер, люблю спорт"
        ),
        new OA\Property(
            property: "phone",
            type: "string",
            nullable: true,
            description: "Номер телефона",
            example: "+79991234567"
        ),
        new OA\Property(
            property: "birth_date",
            type: "string",
            format: "date",
            nullable: true,
            description: "Дата рождения",
            example: "1995-05-15"
        ),
        new OA\Property(
            property: "city",
            type: "string",
            nullable: true,
            description: "Город проживания",
            example: "Москва"
        ),
        new OA\Property(
            property: "email_verified_at",
            type: "string",
            format: "date-time",
            nullable: true,
            description: "Дата подтверждения email",
            example: "2026-06-19T10:00:00.000000Z"
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата регистрации",
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

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'phone',
        'birth_date',
        'city',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    // Связи
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'likes')->withTimestamps();
    }

    public function subscribedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_subscriptions')->withTimestamps();
    }
}





