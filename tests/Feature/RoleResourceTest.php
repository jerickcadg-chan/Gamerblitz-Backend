<?php

namespace Tests\Feature;

use App\Models\Role;
use Tests\TestCase;

class RoleResourceTest extends TestCase
{
    public function testCanShowDataTablePage()
    {
        $response = $this->actingAs($this->user)->get(route('role.index'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::roles.index');
    }

    public function testCanShowCreateRoleForm()
    {
        $response = $this->actingAs($this->user)->get(route('role.create'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::roles.create');
    }

    public function testCanStoreNewRole()
    {
        $role = factory(Role::class)->make();
        $response = $this->actingAs($this->user)->post(route('role.store'), $role->toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);

        $this->assertDatabaseHas('roles', $role->toArray());
        $response->assertRedirect(route('role.index'));
    }

    public function testCanShowRoleDetailPage()
    {
        $role = factory(Role::class)->create();
        $response = $this->actingAs($this->user)->get(route('role.show', $role));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::roles.show');
    }

    public function testCanShowUpdateRoleForm()
    {
        $role = factory(Role::class)->create();
        $response = $this->actingAs($this->user)->get(route('role.edit', $role));

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::roles.edit');
    }

    public function testCanUpdateRole()
    {
        $role = factory(Role::class)->create();
        $newData = factory(Role::class)->make();
        $response = $this->actingAs($this->user)->put(route('role.update', $role), $newData->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('roles', $newData->toArray());
        $response->assertRedirect(route('role.index'));
    }

    public function testCanDeleteRole()
    {
        $role = factory(Role::class)->create();
        $response = $this->actingAs($this->user)->delete(route('role.destroy', $role));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('roles', $role->toArray());

        $response->assertRedirect(route('role.index'));
    }
}
