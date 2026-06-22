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
