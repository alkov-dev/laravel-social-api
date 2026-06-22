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
