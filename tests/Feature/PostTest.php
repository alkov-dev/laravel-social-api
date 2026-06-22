<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_posts_list()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Post::factory()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'user', 'category']
                ],
                'meta' => ['total', 'per_page']
            ]);
    }

    public function test_filter_posts_by_category()
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Post::factory()->create(['user_id' => $user->id, 'category_id' => $category1->id]);
        Post::factory()->create(['user_id' => $user->id, 'category_id' => $category2->id]);

        $response = $this->actingAs($user)->getJson('/api/posts?category_id=' . $category1->id);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_create_post_requires_auth()
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Test',
            'category_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_create_post_success()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title' => 'Мой пост',
            'content' => 'Содержание',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => ['title' => 'Мой пост'],
            ]);
    }

    public function test_create_post_requires_category()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title' => 'Мой пост',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }
}
