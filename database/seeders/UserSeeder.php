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
                'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Alex',
                'bio' => 'Фитнес-тренер, люблю спорт',
                'city' => 'Москва',
            ],
            [
                'name' => 'Мария Иванова',
                'email' => 'maria@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Maria',
                'bio' => 'Путешественница и фотограф',
                'city' => 'Санкт-Петербург',
            ],
            [
                'name' => 'Дмитрий Сидоров',
                'email' => 'dmitry@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Dmitry',
                'bio' => 'Шеф-повар, готовлю с душой',
                'city' => 'Казань',
            ],
            [
                'name' => 'Елена Смирнова',
                'email' => 'elena@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Elena',
                'bio' => 'IT-специалист, люблю технологии',
                'city' => 'Новосибирск',
            ],
            [
                'name' => 'Иван Козлов',
                'email' => 'ivan@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Ivan',
                'bio' => 'Музыкант и путешественник',
                'city' => 'Екатеринбург',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
