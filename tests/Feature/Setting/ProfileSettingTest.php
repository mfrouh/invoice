<?php

namespace Tests\Feature\Setting;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileSettingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_user_can_edit_profile_setting()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->customer)
            ->post(route('profile-setting.store'), ['name' => 'new name', 'email' => $this->customer->email])
            ->assertSuccessful();

        $this->assertEquals($this->customer->name, 'new name');
    }
}
