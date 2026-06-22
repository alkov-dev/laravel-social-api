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
