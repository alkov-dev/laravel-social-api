<?php

namespace App\Http\Controllers;

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
