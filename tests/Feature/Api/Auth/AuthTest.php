<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_api()
    {
        $user = User::factory()->create();

        $response = $this->json('post', route('api.login'), ['email' => $user->email, 'password' => 'password']);

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_api()
    {
        $user = User::factory()->create();

        $response = $this->json('post', route('api.login'), ['email' => $user->email, 'password' => 'wrong-password']);

        $this->assertGuest();
    }

    public function test_user_can_register_api()
    {
        $response = $this->json('post', route('api.register'), [
            'email' => 'customer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'name' => 'customer',
        ]);

        $this->assertAuthenticated();
    }

    public function test_user_cannot_register_api()
    {
        $response = $this->json('post', route('api.register'), [
            'email' => 'customer@example.com',
            'password' => 'wrong-password',
            'password_confirmation' => 'password',
            'name' => 'customer',
        ]);

        $this->assertGuest();
    }

    public function test_user_can_logout_api()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('post', route('api.logout'))
            ->assertJsonPath('message', 'Close App');
    }

}
