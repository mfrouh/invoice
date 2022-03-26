<?php

namespace Tests\Feature\Backend;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_visit_setting_page()
    {
        $this->actingAs($this->admin)
            ->get(route('setting.index'))
            ->assertSimilarJson(['data' => null])
            ->assertSuccessful();

        Setting::create(['name' => 'website name', 'description' => 'description', 'logo' => UploadedFile::fake()->image('avatar.jpg')]);

        $this->actingAs($this->admin)
            ->get(route('setting.index'))
            ->assertJsonPath('data.name', 'website name')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_settings_because_role()
    {
        foreach ([$this->customer] as $user) {
            $this->actingAs($user)
                ->get(route('setting.index'))
                ->assertForbidden();
        }
    }
}
