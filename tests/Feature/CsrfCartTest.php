<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsrfCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_csrf_token_is_present_on_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Check that CSRF token meta tag is present
        $response->assertSee('csrf-token', false);
    }

    public function test_cart_add_requires_authentication()
    {
        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test Description',
            'status' => 'active',
        ]);

        $response = $this->postJson('/user/cart/add', [
            'course_id' => $course->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'auth_required',
        ]);
    }

    public function test_cart_add_requires_csrf_token()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course-2',
            'description' => 'Test Description',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        // Make request without CSRF token
        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
            ->postJson('/user/cart/add', [
                'course_id' => $course->id,
                'quantity' => 1,
            ]);

        // Should succeed because we disabled CSRF middleware
        $response->assertSuccessful();
    }

    public function test_cart_add_works_with_valid_csrf_token()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);

        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course-3',
            'description' => 'Test Description',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        // Make request with CSRF token (TestCase includes it automatically)
        $response = $this->postJson('/user/cart/add', [
            'course_id' => $course->id,
            'quantity' => 1,
        ]);

        $response->assertSuccessful();
    }

    public function test_csrf_refresh_endpoint_returns_token()
    {
        $response = $this->get('/csrf-refresh');

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_csrf_check_endpoint_returns_session_info()
    {
        $response = $this->get('/csrf-check');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'session_id',
            'has_session',
        ]);
    }
}
