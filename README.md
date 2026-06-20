```
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



```
