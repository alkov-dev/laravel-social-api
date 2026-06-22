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
