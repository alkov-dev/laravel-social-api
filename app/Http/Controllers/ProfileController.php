<?php

namespace App\Http\Controllers;

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
