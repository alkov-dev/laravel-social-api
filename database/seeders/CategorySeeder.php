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
                'icon' => '🍳',
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
