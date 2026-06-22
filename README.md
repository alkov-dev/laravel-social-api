```php
┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│    users     │       │  categories  │       │   posts      │
├──────────────┤       ├──────────────┤       ├──────────────┤
│ id (PK)      │       │ id (PK)      │       │ id (PK)      │
│ name         │       │ name         │       │ title        │
│ email        │       │ slug         │       │ content      │
│ password     │       │ description  │       │ user_id (FK) │
│ avatar       │       │ color        │       │ category_id  │
│ bio          │       │ icon         │       │ created_at   │
│ created_at   │       │ created_at   │       │ updated_at   │
│ updated_at   │       └──────────────┘       └──────────────┘
└──────────────┘                                    │
       │                                            │
       │            ┌──────────────────┐            │
       │            │  post_images     │            │
       │            ├──────────────────            │
       │            │ id (PK)          │            │
       │            │ post_id (FK)     │            │
       │            │ preview_url      │            │
       │            │ full_url         │            │
       │            │ alt_text         │            │
       │            │ created_at       │            │
       │            └──────────────────┘            │
       │                                            │
       │         ┌──────────────────┐               │
       │         │      likes       │               │
       │         ├──────────────────               │
       │         │ id (PK)          │               │
       │         │ user_id (FK)     │               │
       │         │ post_id (FK)     │───────────────┘
       │         │ created_at       │
       │         └──────────────────┘
       │
       │         ──────────────────┐
       │         │    comments      │
       │         ├──────────────────┤
       │         │ id (PK)          │
       │         │ user_id (FK)     │
       │         │ post_id (FK)     │
       │         │ parent_id (FK)   │ ← для ответов
       │         │ content          │
       │         │ created_at       │
       │         │ updated_at       │
       │         └──────────────────┘

-----------------------
create_categories_table

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // HEX цвет
            $table->string('icon')->nullable(); // иконка (emoji или SVG)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
-----------------------
update_users_table 

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('phone')->nullable()->after('bio');
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('city')->nullable()->after('birth_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio', 'phone', 'birth_date', 'city']);
        });
    }
};
-----------------------
create_posts_table

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            // Индексы для фильтрации
            $table->index('published_at');
            $table->index('category_id');
            $table->index(['category_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
-----------------------
create_post_images_table
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('preview_url');  // превью (thumbnail)
            $table->string('full_url');     // полный размер
            $table->string('alt_text')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_images');
    }
};
-----------------------
create_likes_table

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Один пользователь может лайкнуть пост только один раз
            $table->unique(['user_id', 'post_id']);
            $table->index('post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
-----------------------
create_comments_table

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('comments')
                  ->cascadeOnDelete(); // если удалят родителя, удалятся и ответы
            $table->text('content');
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();

            $table->index('post_id');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

-----------------------
create_subscriptions_table (для WebSocket)

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_subscriptions');
    }
};
-----------------------
Модели и связи
User.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'bio', 'phone', 'birth_date', 'city',
    ];

    protected $hidden = ['password', 'remember_token'];

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
-----------------------
Category.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
-----------------------
Post.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

-----------------------
PostImage.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
-----------------------
Like.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id', 'post_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
-----------------------
Comment.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
-----------------------
🌱 Сидеры (Seed)
DatabaseSeeder.php

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
        ]);
    }
}
-----------------------
UserSeeder.php

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Александр Петров',
                'email' => 'alex@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://avatars.mds.yandex.net/i?id=8ea41d01dd6a82a0fb5d9e097ebe42cba7e83841-7863956-images-thumbs&n=13',
                'bio' => 'Фитнес-тренер, люблю спорт',
                'city' => 'Москва',
            ],
            [
                'name' => 'Мария Иванова',
                'email' => 'maria@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://i.pinimg.com/736x/b3/25/0c/b3250c8eb64956a5ebb2f277f46a6285.jpg',
                'bio' => 'Путешественница и фотограф',
                'city' => 'Санкт-Петербург',
            ],
            [
                'name' => 'Дмитрий Сидоров',
                'email' => 'dmitry@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://avatars.mds.yandex.net/i?id=ea184ad7b45ffd28f9390f1c95dcb18956efa943-5911267-images-thumbs&n=13',
                'bio' => 'Шеф-повар, готовлю с душой',
                'city' => 'Казань',
            ],
            [
                'name' => 'Елена Смирнова',
                'email' => 'elena@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://avatars.mds.yandex.net/i?id=c8b53513c0ea059eec2cb98ec10651aad70cc9e4-4576320-images-thumbs&n=13',
                'bio' => 'IT-специалист, люблю технологии',
                'city' => 'Новосибирск',
            ],
            [
                'name' => 'Иван Козлов',
                'email' => 'ivan@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://avatars.mds.yandex.net/i?id=286851b352571a9d3c483b76dc589e801a499a47-12737181-images-thumbs&n=13',
                'bio' => 'Музыкант и путешественник',
                'city' => 'Екатеринбург',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}


-----------------------
CategorySeeder.php

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Фитнес',
                'description' => 'Тренировки, спорт, здоровый образ жизни',
                'color' => '#EF4444',
                'icon' => '💪',
            ],
            [
                'name' => 'Путешествия',
                'description' => 'Путешествия, приключения, новые места',
                'color' => '#3B82F6',
                'icon' => '✈️',
            ],
            [
                'name' => 'Кулинария',
                'description' => 'Рецепты, готовка, гастрономия',
                'color' => '#F59E0B',
                'icon' => '',
            ],
            [
                'name' => 'Технологии',
                'description' => 'IT, гаджеты, программирование',
                'color' => '#10B981',
                'icon' => '💻',
            ],
            [
                'name' => 'Музыка',
                'description' => 'Концерты, альбомы, музыкальные события',
                'color' => '#8B5CF6',
                'icon' => '🎵',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'color' => $category['color'],
                'icon' => $category['icon'],
            ]);
        }
    }
}
-----------------------
PostSeeder.php

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        $posts = [
            [
                'title' => 'Топ-5 упражнений для пресса',
                'content' => 'Делюсь своей любимой тренировкой для пресса. Эти упражнения помогли мне добиться результата за 3 месяца. Главное — регулярность и правильное питание!',
                'user_id' => $users[0]->id,
                'category_id' => $categories[0]->id, // Фитнес
                'published_at' => now()->subDays(1),
                'images' => [
                    [
                        'preview' => 'https://avatars.mds.yandex.net/i?id=526b7201a96cecc969aada855112fcf90d38b5db-7017675-images-thumbs&n=13',
                        'full' => 'https://avatars.mds.yandex.net/i?id=526b7201a96cecc969aada855112fcf90d38b5db-7017675-images-thumbs&n=13',
                        'alt' => 'Упражнения для пресса',
                    ],
                ],
            ],
            [
                'title' => 'Мое путешествие в Японию',
                'content' => 'Япония — это невероятная страна! Сакура, храмы, вкуснейшая еда. Делюсь фотографиями и впечатлениями из поездки.',
                'user_id' => $users[1]->id,
                'category_id' => $categories[1]->id, // Путешествия
                'published_at' => now()->subDays(3),
                'images' => [
                    [
                        'preview' => 'https://avatars.mds.yandex.net/i?id=5636e031fa49e98ed312ab9d8f5cab0f7af3f021-5232025-images-thumbs&n=13',
                        'full' => 'https://avatars.mds.yandex.net/i?id=5636e031fa49e98ed312ab9d8f5cab0f7af3f021-5232025-images-thumbs&n=13',
                        'alt' => 'Сакура в Токио',
                    ],
                    [
                        'preview' => 'https://avatars.mds.yandex.net/i?id=5636e031fa49e98ed312ab9d8f5cab0f7af3f021-5232025-images-thumbs&n=13',
                        'full' => 'https://avatars.mds.yandex.net/i?id=5636e031fa49e98ed312ab9d8f5cab0f7af3f021-5232025-images-thumbs&n=13',
                        'alt' => 'Храм в Киото',
                    ],
                ],
            ],
            [
                'title' => 'Рецепт идеальной пасты карбонара',
                'content' => 'Классическая итальянская паста карбонара без сливок! Только яйца, пармезан, гуанчиале и черный перец. Пошаговый рецепт с фото.',
                'user_id' => $users[2]->id,
                'category_id' => $categories[2]->id, // Кулинария
                'published_at' => now()->subDays(5),
                'images' => [
                    [
                        'preview' => 'https://i.pinimg.com/736x/b7/ae/97/b7ae9781175cff82575efc1017fa4dec.jpg',
                        'full' => 'https://i.pinimg.com/736x/b7/ae/97/b7ae9781175cff82575efc1017fa4dec.jpg',
                        'alt' => 'Паста карбонара',
                    ],
                ],
            ],
            [
                'title' => 'Обзор нового MacBook Pro M3',
                'content' => 'Тестирую новый MacBook Pro на чипе M3 уже две недели. Делюсь впечатлениями о производительности, экране и автономности.',
                'user_id' => $users[3]->id,
                'category_id' => $categories[3]->id, // Технологии
                'published_at' => now()->subDays(7),
                'images' => [
                    [
                        'preview' => 'https://i.pinimg.com/736x/a2/31/43/a231439d3011607ac6e67a29424c9ae0.jpg',
                        'full' => 'https://i.pinimg.com/736x/a2/31/43/a231439d3011607ac6e67a29424c9ae0.jpg',
                        'alt' => 'MacBook Pro M3',
                    ],
                ],
            ],
            [
                'title' => 'Отчет с концерта Coldplay',
                'content' => 'Вчера был на концерте Coldplay в Москве! Это было незабываемо. Делюсь фото и видео с выступления.',
                'user_id' => $users[4]->id,
                'category_id' => $categories[4]->id, // Музыка
                'published_at' => now()->subHours(12),
                'images' => [
                    [
                        'preview' => 'https://images.wallpaperscraft.com/image/single/night_city_street_skyscrapers_133350_2560x1080.jpg',
                        'full' => 'https://images.wallpaperscraft.com/image/single/night_city_street_skyscrapers_133350_2560x1080.jpg',
                        'alt' => 'Coldplay на сцене',
                    ],
                    [
                        'preview' => 'https://i.pinimg.com/736x/a2/31/43/a231439d3011607ac6e67a29424c9ae0.jpg',
                        'full' => 'https://i.pinimg.com/736x/a2/31/43/a231439d3011607ac6e67a29424c9ae0.jpg',
                        'alt' => 'Зрители на концерте',
                    ],
                ],
            ],
        ];

        foreach ($posts as $postData) {
            $images = $postData['images'];
            unset($postData['images']);

            $post = Post::create($postData);

            foreach ($images as $index => $image) {
                PostImage::create([
                    'post_id' => $post->id,
                    'preview_url' => $image['preview'],
                    'full_url' => $image['full'],
                    'alt_text' => $image['alt'],
                    'order' => $index,
                ]);
            }
        }
    }
}

-----------------------
🚀 Запуск

# 1. Создать миграции
php artisan migrate

# 2. Запустить сидеры
php artisan db:seed

# Или всё сразу (с пересозданием БД)
php artisan migrate:fresh --seed


API контроллеры:
PostController — CRUD постов, лента, фильтрация
CategoryController — список категорий
CommentController — комментарии и ответы
LikeController — лайки
ProfileController — редактирование профиля
UploadController — загрузка картинок
WebSocket события:
PostLiked — обновление счётчика лайков
CommentAdded — новый комментарий
ReplyAdded — ответ на комментарий


app/Http/Controllers/Api/
├── PostController.php
├── CategoryController.php
├── CommentController.php
├── LikeController.php
├── ProfileController.php
└── UploadController.php

1️⃣ API Resources (форматирование ответов)
-----------------------
PostResource.php

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
-----------------------
UserResource.php

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'phone' => $this->phone,
            'city' => $this->city,
            'birth_date' => $this->birth_date?->toDateString(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
-----------------------
CategoryResource.php

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
        ];
    }
}
-----------------------
PostImageResource.php
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
-----------------------
CommentResource.php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'likes_count' => $this->likes_count,
            'user' => new UserResource($this->whenLoaded('user')),
            'replies' => CommentResource::collection($this->whenLoaded('nestedReplies')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}

2️⃣ Контроллеры
-----------------------
CategoryController.php

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::all();
        
        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
        ]);
    }
}
-----------------------
PostController.php

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Лента постов с фильтрацией
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::with(['user', 'category', 'firstImage'])
            ->where('is_published', true);

        // Фильтр по категории
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Фильтр по дате (от)
        if ($request->filled('date_from')) {
            $query->where('published_at', '>=', $request->date_from);
        }

        // Фильтр по дате (до)
        if ($request->filled('date_to')) {
            $query->where('published_at', '<=', $request->date_to);
        }

        // Сортировка
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['published_at', 'created_at', 'likes_count', 'comments_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $posts = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['user', 'category', 'images']);

        return response()->json([
            'success' => true,
            'data' => new PostResource($post),
        ]);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'published_at' => now(),
            'is_published' => true,
        ]);

        // Обработка картинок
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                // Сохраняем на диске 'public'
                $previewPath = $file->store('posts/previews', 'public');
                $fullPath = $file->store('posts/full', 'public');

                $post->images()->create([
                    // ✅ Полный URL через asset()
                    'preview_url' => asset(Storage::url($previewPath)),
                    'full_url' => asset(Storage::url($fullPath)),
                    'alt_text' => $request->input("alt_texts.{$index}"),
                    'order' => $index,
                ]);
            }
        }

        $post->load(['user', 'category', 'images']);

        return response()->json([
            'success' => true,
            'message' => 'Пост успешно создан',
            'data' => new PostResource($post),
        ], 201);
    }

    /**
     * Обновление поста
     */
    public function update(StorePostRequest $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступа',
            ], 403);
        }

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
        ]);

        $post->load(['user', 'category', 'images']);

        return response()->json([
            'success' => true,
            'message' => 'Пост обновлён',
            'data' => new PostResource($post),
        ]);
    }

    /**
     * Удаление поста
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
    // Проверяем, что пользователь — автор поста
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав на удаление этого поста',
            ], 403);
        }

        // Удаляем связанные изображения
        foreach ($post->images as $image) {
            // Удаляем файлы с диска
            $previewPath = str_replace(asset('storage/') , '', $image->preview_url);
            $fullPath = str_replace(asset('storage/') , '', $image->full_url);

            Storage::disk('public')->delete($previewPath);
            Storage::disk('public')->delete($fullPath);
        }

        $post->images()->delete();

        // Удаляем лайки и комментарии
        $post->likes()->delete();
        $post->comments()->delete();

        // Удаляем пост
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Пост успешно удалён',
        ]);
    }
}


-----------------------
LikeController.php

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Поставить/снять лайк
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        
        $existingLike = Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            // Снимаем лайк
            $existingLike->delete();
            $post->decrement('likes_count');
            $isLiked = false;
            
            // WebSocket событие
            broadcast(new \App\Events\PostUnliked($post));
        } else {
            // Ставим лайк
            Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $post->increment('likes_count');
            $isLiked = true;
            
            // WebSocket событие
            broadcast(new \App\Events\PostLiked($post, $user));
        }

        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $post->likes_count,
        ]);
    }

    /**
     * Список лайкнувших пользователей
     */
    public function users(Post $post): JsonResponse
    {
        $users = $post->likes()->with('user')->get()->pluck('user');

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }
}


-----------------------
CommentController.php

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Список комментариев поста (с ответами)
     */
    public function index(Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->with(['user', 'nestedReplies.user'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => CommentResource::collection($comments),
        ]);
    }

    /**
     * Создать комментарий или ответ
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Проверка, что parent_id относится к этому посту
        if ($request->parent_id) {
            $parent = Comment::findOrFail($request->parent_id);
            if ($parent->post_id !== $post->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Неверный parent_id',
                ], 422);
            }
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        $post->increment('comments_count');

        $comment->load(['user', 'replies.user']);

        // WebSocket событие
        broadcast(new \App\Events\CommentAdded($comment, $post));

        return response()->json([
            'success' => true,
            'message' => 'Комментарий добавлен',
            'data' => new CommentResource($comment),
        ], 201);
    }

    /**
     * Обновить комментарий
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступа',
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update(['content' => $request->content]);

        return response()->json([
            'success' => true,
            'message' => 'Комментарий обновлён',
            'data' => new CommentResource($comment->load('user')),
        ]);
    }

    /**
     * Удалить комментарий
     */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступа',
            ], 403);
        }

        $post = $comment->post;
        $comment->delete();
        $post->decrement('comments_count');

        return response()->json([
            'success' => true,
            'message' => 'Комментарий удалён',
        ]);
    }
}

-----------------------
ProfileController.php

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Получить текущий профиль
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * Обновить профиль
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();
        $data = $request->only(['name', 'bio', 'phone', 'city', 'birth_date']);

        // Обработка аватарки
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('public/avatars');
            $data['avatar'] = asset('storage/' . $path);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Профиль обновлён',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Получить профиль другого пользователя
     */
    public function showUser(int $userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }
}

-----------------------
UploadController.php

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Загрузка превью и полной версии картинки
     */
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120', // до 5MB
        ]);

        $file = $request->file('image');

        // Генерируем превью (400x300)
        $previewPath = $file->store('public/posts/previews');
        
        // Сохраняем оригинал
        $fullPath = $file->store('public/posts/full');

        return response()->json([
            'success' => true,
            'data' => [
                'preview_url' => asset('storage/' . $previewPath),
                'full_url' => asset('storage/' . $fullPath),
            ],
        ]);
    }

    /**
     * Удаление файла
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $path = str_replace(asset('storage/'), '', $request->url);
        Storage::disk('public')->delete($path);

        return response()->json([
            'success' => true,
            'message' => 'Файл удалён',
        ]);
    }
}

3️⃣ Form Request для валидации
-----------------------
StorePostRequest.php

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'alt_texts' => ['nullable', 'array'],
            'alt_texts.*' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Категория обязательна',
            'category_id.exists' => 'Категория не найдена',
            'images.max' => 'Максимум 5 картинок',
        ];
    }
}

4️⃣ Маршруты API
-----------------------
<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

// Публичные маршруты
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Защищённые маршруты (требуют авторизации)
Route::middleware('auth:sanctum')->group(function () {
    
    // Посты
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // Лайки
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);
    Route::get('/posts/{post}/likes', [LikeController::class, 'users']);

    // Комментарии
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Профиль
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/users/{user}', [ProfileController::class, 'showUser']);

    // Загрузка файлов
    Route::post('/upload/image', [UploadController::class, 'image']);
    Route::delete('/upload', [UploadController::class, 'destroy']);
});

5️⃣ WebSocket события

php artisan make:event PostLiked
php artisan make:event CommentAdded
php artisan make:event PostUnliked
php artisan make:listener SendNotificationListener --event=PostLiked Создать Listener (слушатель)
-----------------------
PostLiked.php

<?php

namespace App\Events;

use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostLiked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Post $post,
        public User $user
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
            'post_id' => $this->post->id,
            'likes_count' => $this->post->likes_count,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ],
        ];
    }
}

-----------------------
PostUnliked.php

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

-----------------------
CommentAdded.php

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

6️ Настройка Broadcasting .env
-----------------------
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

-----------------------
php artisan install:broadcasting

config/broadcasting.php

'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ],
    ],
],
-----------------------

8️ Тестирование API
tests/Feature/PostTest.php

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_posts_list()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        Post::factory()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'user', 'category']
                ],
                'meta' => ['total', 'per_page']
            ]);
    }

    public function test_filter_posts_by_category()
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Post::factory()->create(['user_id' => $user->id, 'category_id' => $category1->id]);
        Post::factory()->create(['user_id' => $user->id, 'category_id' => $category2->id]);

        $response = $this->getJson('/api/posts?category_id=' . $category1->id);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_create_post_requires_auth()
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Test',
            'category_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_create_post_success()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title' => 'Мой пост',
            'content' => 'Содержание',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => ['title' => 'Мой пост'],
            ]);
    }

    public function test_create_post_requires_category()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title' => 'Мой пост',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }
}



9️⃣ Factory для тестов
-----------------------
<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'likes_count' => fake()->numberBetween(0, 100),
            'comments_count' => fake()->numberBetween(0, 50),
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'is_published' => true,
        ];
    }
}

-----------------------
<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'color' => fake()->hexColor(),
            'icon' => fake()->randomElement(['💪', '️', '🍕', '💻', '', '', '🎨', '⚽']),
        ];
    }

    /**
     * Фитнес категория
     */
    public function fitness(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Фитнес',
            'slug' => 'fitnes',
            'description' => 'Тренировки, спорт, здоровый образ жизни',
            'color' => '#EF4444',
            'icon' => '💪',
        ]);
    }

    /**
     * Категория путешествий
     */
    public function travel(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Путешествия',
            'slug' => 'puteshestviya',
            'description' => 'Путешествия, приключения, новые места',
            'color' => '#3B82F6',
            'icon' => '✈️',
        ]);
    }

    /**
     * Кулинарная категория
     */
    public function cooking(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Кулинария',
            'slug' => 'kulinariya',
            'description' => 'Рецепты, готовка, гастрономия',
            'color' => '#F59E0B',
            'icon' => '🍕',
        ]);
    }

    /**
     * Технологическая категория
     */
    public function technology(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Технологии',
            'slug' => 'tekhnologii',
            'description' => 'IT, гаджеты, программирование',
            'color' => '#10B981',
            'icon' => '💻',
        ]);
    }

    /**
     * Музыкальная категория
     */
    public function music(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Музыка',
            'slug' => 'muzyka',
            'description' => 'Концерты, альбомы, музыкальные события',
            'color' => '#8B5CF6',
            'icon' => '🎵',
        ]);
    }
}
-----------------------
Натройка bootstrap.php

    <?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ Настраиваем CORS
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // ✅ Отключаем CSRF для API 
        $middleware->preventRequestForgery(except: [
            'api/*',
        ]);

        // ✅ Sanctum для работы с SPA
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();


-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------
-----------------------


🚀 Запуск
# 1. Миграции
php artisan migrate

# 2. Сидеры
php artisan db:seed

# 3. Символическая ссылка для storage
php artisan storage:link

# 4. Запуск Laravel
php artisan serve

# 5. Запуск WebSocket (если используется)
php artisan websockets:serve
-----------------------
```
