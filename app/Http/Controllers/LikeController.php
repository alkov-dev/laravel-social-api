<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Поставить/снять лайк
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        $existingLike = Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            // Снимаем лайк
            $existingLike->delete();
            $post->decrement('likes_count');
            $isLiked = false;

            // WebSocket событие
            broadcast(new \App\Events\PostUnliked($post));
        } else {
            // Ставим лайк
            Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $post->increment('likes_count');
            $isLiked = true;

            // WebSocket событие
            broadcast(new \App\Events\PostLiked($post, $user));
        }

        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $post->likes_count,
        ]);
    }

    /**
     * Список лайкнувших пользователей
     */
    public function users(Post $post): JsonResponse
    {
        $users = $post->likes()->with('user')->get()->pluck('user');

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }
}
