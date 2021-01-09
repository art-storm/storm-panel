<?php

namespace Tests\Feature\Admin;

use App\User;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\UserRole;
use App\Traits\Tests\RoleTrait;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RoleTrait;

    private $admin;

    // Permission for admin login
    private $permission_id = 5;

    // Permissions for roles view/create/update/delete
    private $permissions = [10, 11, 12, 13];

    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
        $this->admin = Auth::user();
    }

    /**
     * Test authorized user can view role list
     */
    public function testUserCanViewRoleList()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.roles.index'));
        $response->assertSuccessful();
        $response->assertViewIs('admin.roles');
    }

    /**
     * Test authorized user can view role card
     */
    public function testUserCanViewRoleCard()
    {
        $role = $this->createRole();

        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.roles.show', ['role_id' => $role->id]));
        $response->assertSuccessful();
        $response->assertViewIs('admin.roles_show');
        $role->delete();
    }

    /**
     * Test authorized user can view role create form
     */
    public function testUserCanViewRoleCreateForm()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.roles.create'));
        $response->assertSuccessful();
        $response->assertViewIs('admin.roles_create');
    }

    /**
     * Test authorized user can create role
     */
    public function testUserCanCreateRole()
    {
        $user = $this->admin;

        $post = [
            'role_name' => 'test-name',
            'role_display' => 'test display',
            'permissions' => [$this->permission_id],
        ];

        $response = $this->actingAs($user)->post(route('admin.roles.store'), $post);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('status');

        $role_db = Role::where('role_name', '=', $post['role_name'])->first();
        $this->assertTrue($role_db->role_name == $post['role_name']);
        $role_db->delete();
    }

    /**
     * Test authorized user can view role edit form
     */
    public function testUserCanViewRoleEditForm()
    {
        $role = $this->createRole();

        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.roles.edit', ['role_id' => $role->id]));
        $response->assertSuccessful();
        $response->assertViewIs('admin.roles_edit');
        $role->delete();
    }

    /**
     * Test authorized user can update role
     */
    public function testUserCanUpdateRole()
    {
        $role = $this->createRole();

        $post = [
            'role_id' => $role->id,
            'role_name' => 'test-name-update',
            'role_display' => 'test display update',
            'permissions' => [$this->permission_id],
        ];

        $user = $this->admin;
        $response = $this->actingAs($user)->put(route('admin.roles.update', $post));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('status');

        $role_db = Role::where('role_name', '=', $post['role_name'])->first();
        $this->assertTrue($role_db->role_name == $post['role_name']);

        $role->delete();
    }

    /**
     * Test authorized user can delete role
     */
    public function testUserCanDeleteRole()
    {
        $role = $this->createRole();

        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.roles.destroy', ['role_id' => $role->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('status');

        $role_db = Role::where('role_name', '=', $role->role_name)->first();
        $this->assertNull($role_db);

        $role_permissions = PermissionRole::where('role_id', '=', $role->id)->first();
        $this->assertNull($role_permissions);

        $role_user = UserRole::where('role_id', '=', $role->id)->first();
        $this->assertNull($role_user);
    }

    /**
     * Test permissions for authorized user
     */
    public function testPermissionForUserAuthorized()
    {
        $role = $this->createRole($this->permissions);
        $user = factory(User::class)->create([
            'role_id' => $role->id,
        ]);

        // Can view role list
        $response = $this->actingAs($user)->get(route('admin.roles.index'));
        $response->assertSuccessful();

        // Can view role card
        $response = $this->actingAs($user)->get(route('admin.roles.show', ['role_id' => $role->id]));
        $response->assertSuccessful();

        // Can view role create form
        $response = $this->actingAs($user)->get(route('admin.roles.create'));
        $response->assertSuccessful();

        // Can view role edit form
        $response = $this->actingAs($user)->get(route('admin.roles.edit', ['role_id' => $role->id]));
        $response->assertSuccessful();

        // Can store role
        $post = [
            'role_name' => 'test-name',
            'role_display' => 'test display',
            'permissions' => [$this->permission_id],
        ];
        $response = $this->actingAs($user)->post(route('admin.roles.store'), $post);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.roles.index'));
        Role::where('role_name', '=', $post['role_name'])->delete();

        // Can update role
        $post['role_id'] = $role->id;
        $response = $this->actingAs($user)->put(route('admin.roles.update', $post));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.roles.index'));

        // Can delete role
        $response = $this->actingAs($user)->get(route('admin.roles.destroy', ['role_id' => $role->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.roles.index'));

        $user->delete();
        $role->delete();
    }

    /**
     * Test permissions for non authorized user
     */
    public function testPermissionForUserNonAuthorized()
    {
        $role = $this->createRole();
        $user = factory(User::class)->create([
            'role_id' => $role->id,
        ]);

        // Can not view role list
        $response = $this->actingAs($user)->get(route('admin.roles.index'));
        $response->assertStatus(403);

        // Can not view role card
        $response = $this->actingAs($user)->get(route('admin.roles.show', ['role_id' => $role->id]));
        $response->assertStatus(403);

        // Can not view role create form
        $response = $this->actingAs($user)->get(route('admin.roles.create'));
        $response->assertStatus(403);

        // Can not view role edit form
        $response = $this->actingAs($user)->get(route('admin.roles.edit', ['role_id' => $role->id]));
        $response->assertStatus(403);

        // Can not create role
        $post = [
            'role_name' => 'test-name',
            'role_display' => 'test display',
            'permissions' => [$this->permission_id],
        ];
        $response = $this->actingAs($user)->post(route('admin.roles.store'), $post);
        $response->assertStatus(403);

        // Can not update role
        $post['role_id'] = $role->id;
        $response = $this->actingAs($user)->put(route('admin.roles.update', $post));
        $response->assertStatus(403);

        // Can not delete role
        $response = $this->actingAs($user)->get(route('admin.roles.destroy', ['role_id' => $role->id]));
        $response->assertStatus(403);

        $user->delete();
        $role->delete();
    }
}
