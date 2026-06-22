<?php

namespace App\Http\Controllers;

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
