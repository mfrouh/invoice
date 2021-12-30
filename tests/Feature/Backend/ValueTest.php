<?php

namespace Tests\Feature\Backend;

use App\Models\Attribute;
use App\Models\User;
use App\Models\Value;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_get_all_values()
    {
        $this->actingAs($this->admin)
            ->get(route('value.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Value::factory(12)->create([
            'attribute_id' => Attribute::factory()->create()->id]);

        $this->actingAs($this->admin)
            ->get(route('value.index', ['attribute_id' => 1]))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_all_routes_value_because_role()
    {
        foreach ([$this->customer] as $user) {
            $this->actingAs($user)
                ->get(route('value.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('value.store'), ['value' => 'value'])
                ->assertForbidden();

            $value = Value::factory()->create();

            $this->actingAs($user)
                ->get(route('value.show', [$value->id]))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('value.update', [$value->id]), ['value' => 'value2'])
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('value.destroy', [$value->id]))
                ->assertForbidden();
        }
    }

    public function test_admin_can_create_value_success()
    {
        $this->actingAs($this->admin)
            ->post(route('value.store'), ['value' => 'value', 'attribute_id' => Attribute::factory()->create()->id])
            ->assertCreated();

        $this->assertDatabaseHas('values', ['value' => 'value']);
    }

    public function test_failed_to_create_value()
    {

        $this->actingAs($this->admin)
            ->post(route('value.store'), ['value' => ''])
            ->assertSessionHasErrors('value');

        $value = Value::create(['attribute_id' => Attribute::factory()->create()->id, 'value' => 'value']);

        $this->actingAs($this->admin)
            ->post(route('value.store'), ['value' => 'value', 'attribute_id' => $value->attribute_id])
            ->assertSessionHasErrors('value');

        $this->actingAs($this->admin)
            ->post(route('value.store'), ['value' => 'value', 'attribute_id' => 2])
            ->assertSessionHasErrors('attribute_id');

    }

    public function test_admin_can_update_value_success()
    {
        $value = Value::create(['attribute_id' => Attribute::factory()->create()->id, 'value' => 'value']);

        $this->actingAs($this->admin)
            ->put(route('value.update', [$value->id]), ['value' => 'value4', 'attribute_id' => $value->attribute_id])
            ->assertSuccessful();

        $this->assertDatabaseHas('values', ['value' => 'value4']);
    }

    public function test_failed_to_update_value()
    {
        $value = Value::create(['attribute_id' => Attribute::factory()->create()->id, 'value' => 'value']);

        $this->actingAs($this->admin)
            ->put(route('value.update', [$value->id]), ['value' => '', 'attribute_id' => $value->attribute_id])
            ->assertSessionHasErrors('value');

        $this->actingAs($this->admin)
            ->put(route('value.update', [$value->id]), ['value' => '', 'attribute_id' => 2])
            ->assertSessionHasErrors('attribute_id');

        $this->actingAs($this->admin)
            ->put(route('value.update', [$value->id]), ['value' => 'value', 'attribute_id' => $value->attribute_id])
            ->assertSessionHasNoErrors('value');
    }

    public function test_admin_can_show_value_success()
    {
        $value = Value::create(['attribute_id' => Attribute::factory()->create()->id, 'value' => 'value']);

        $this->actingAs($this->admin)
            ->get(route('value.show', [$value->id]))
            ->assertJsonPath('data.value', $value->value)
            ->assertSuccessful();
    }

    public function test_admin_can_destroy_value_success()
    {
        $value = Value::create(['attribute_id' => Attribute::factory()->create()->id, 'value' => 'value']);

        $this->actingAs($this->admin)
            ->delete(route('value.destroy', [$value->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('values', ['value' => 'value']);
    }

}
