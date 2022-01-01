<?php

namespace Tests\Feature\Setting;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_user_can_change_password()
    {
        $response = $this->actingAs($this->customer)
            ->post(route('change-password.store'), [
                'old_password' => 'password',
                'password' => 'newPassword',
                'password_confirmation' => 'newPassword']);

        $response->assertSuccessful();

        $response = $this->actingAs($this->customer)
            ->post(route('logout'));
        $this->assertGuest();

        $this->post(route('login'), ['password' => 'newPassword', 'email' => $this->customer->email]);
        $this->assertAuthenticated();
    }

    public function test_user_cannot_change_password()
    {
        $response = $this->actingAs($this->customer)
            ->post(route('change-password.store'), [
                'old_password' => 'wrongPassword',
                'password' => 'newPassword',
                'password_confirmation' => 'newPassword']);

        $response->assertSessionHasErrors('old_password');
        $this->assertAuthenticated();

        $response = $this->actingAs($this->customer)
            ->post(route('logout'));
        $this->assertGuest();

        $this->post(route('login'), ['password' => 'newPassword', 'email' => $this->customer->email]);
        $this->assertGuest();
    }
}
