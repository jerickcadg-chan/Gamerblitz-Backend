<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    public function testCanShowDataTablePage()
    {
        $response = $this->actingAs($this->user)->get(route('user.index'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::users.index');
    }

    public function testCanShowCreateUserForm()
    {
        $response = $this->actingAs($this->user)->get(route('user.create'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::users.create');
    }

    public function testCanStoreNewUser()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($this->user)->post(route('user.store'), $user->toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', $user->toArray());
        $response->assertRedirect(route('user.index'));
    }

    public function testCanShowUserDetailPage()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($this->user)->get(route('user.show', $user));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::users.show');
    }

    public function testCanShowUpdateUserForm()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($this->user)->get(route('user.edit', $user));

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::users.edit');
    }

    public function testCanUpdateUser()
    {
        $user = factory(User::class)->create();
        $newData = factory(User::class)->make();
        $response = $this->actingAs($this->user)->put(route('user.update', $user), $newData->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('users', $newData->toArray());
        $response->assertRedirect(route('user.index'));
    }

    public function testCanDeleteUser()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($this->user)->delete(route('user.destroy', $user));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('users', $user->toArray());

        $response->assertRedirect(route('user.index'));
    }
}
