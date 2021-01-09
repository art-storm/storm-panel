<?php

namespace Tests\Feature\Admin;

use App\User;
use App\Models\UserRole;
use App\Traits\Tests\RoleTrait;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RoleTrait;

    private $admin;

    // Permission for admin login
    private $permission_id = 5;

    // Permissions for roles view/create/update/delete
    private $permissions = [6, 7, 8, 9];

    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
        $this->admin = Auth::user();
    }

    /**
     * Test authorized user can view user list
     */
    public function testUserCanViewUserList()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.users.index'));
        $response->assertSuccessful();
        $response->assertViewIs('admin.users');
    }

    /**
     * Test authorized user can view user card
     */
    public function testUserCanViewUserCard()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.users.show', ['user_id' => $user->id]));
        $response->assertSuccessful();
        $response->assertViewIs('admin.users_show');
    }

    /**
     * Test authorized user can view user create form
     */
    public function testUserCanViewUserCreateForm()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.users.create'));
        $response->assertSuccessful();
        $response->assertViewIs('admin.users_create');
    }

    /**
     * Test authorized user can create user
     */
    public function testUserCanCreateUser()
    {
        $faker = Faker::create();

        $post = [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'password' => 'password',
            'role_id' => '2',
            'roles_additional' => [2],
        ];

        $admin = $this->admin;
        $response = $this->actingAs($admin)->post(route('admin.users.store', $post));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status');

        $user_db = User::with('additionalRoles')->where('email', $post['email'])->first();
        $this->assertTrue($user_db->name == $post['name']);
        $this->assertTrue($user_db->email == $post['email']);
        $this->assertTrue($user_db->role_id == $post['role_id']);
        $this->assertTrue($user_db->additionalRoles()->pluck('role_id')->toArray() == $post['roles_additional']);

        $user_db->delete();
    }

    /**
     * Test authorized user can view user edit form
     */
    public function testUserCanViewUserEditForm()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.users.edit', ['user_id' => $user->id]));
        $response->assertSuccessful();
        $response->assertViewIs('admin.users_edit');
    }

    /**
     * Test authorized user can update user
     */
    public function testUserCanUpdateUser()
    {
        $faker = Faker::create();

        $user = factory(User::class)->create([
            'role_id' => 1,
        ]);

        $post = [
            'user_id' => $user->id,
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'role_id' => '2',
            'roles_additional' => [2],
        ];

        $admin = $this->admin;
        $response = $this->actingAs($admin)->put(route('admin.users.update', $post));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status');

        $user_db = User::with('additionalRoles')->where('id', $user->id)->first();
        $this->assertTrue($user_db->name == $post['name']);
        $this->assertTrue($user_db->email == $post['email']);
        $this->assertTrue($user_db->role_id == $post['role_id']);
        $this->assertTrue($user_db->additionalRoles()->pluck('role_id')->toArray() == $post['roles_additional']);

        $user_db->delete();
    }

    /**
     * Test authorized user can delete user
     */
    public function testUserCanDeleteUser()
    {
        // Additional roles for test
        $roles = [2];

        $user = Event::fakeFor(function () use ($roles) {
            $user = factory(User::class, 1)
                ->create()
                ->each(function ($u) use ($roles) {
                    $u->additionalRoles()->attach($roles);
                });
            return $user[0];
        });

        $admin = $this->admin;
        $response = $this->actingAs($admin)->get(route('admin.users.destroy', ['user_id' => $user->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status');

        $user_db = User::where('id', $user->id)->first();
        $this->assertNull($user_db);

        $role_user = UserRole::where('user_id', $user->id)->first();
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

        // Can view user list
        $response = $this->actingAs($user)->get(route('admin.users.index'));
        $response->assertSuccessful();

        // Can view user card
        $response = $this->actingAs($user)->get(route('admin.users.show', ['user_id' => $user->id]));
        $response->assertSuccessful();

        // Can view user create form
        $response = $this->actingAs($user)->get(route('admin.users.create'));
        $response->assertSuccessful();

        // Can store user
        $post = [
            'name' => 'test-name',
            'email' => 'email@test.test',
            'password' => 'password',
            'role_id' => 2,
        ];
        $response = $this->actingAs($user)->post(route('admin.users.store', $post));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.users.index'));
        User::where('email', '=', $post['email'])->delete();

        // Can view user edit form
        $response = $this->actingAs($user)->get(route('admin.users.edit', ['user_id' => $user->id]));
        $response->assertSuccessful();

        // Can update user
        $post = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ];
        $response = $this->actingAs($user)->put(route('admin.users.update', $post));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.users.index'));

        // Can delete user
        $response = $this->actingAs($user)->get(route('admin.users.destroy', ['user_id' => $user->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.users.index'));

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

        // Can not view user list
        $response = $this->actingAs($user)->get(route('admin.users.index'));
        $response->assertStatus(403);

        // Can not view user card
        $response = $this->actingAs($user)->get(route('admin.users.show', ['user_id' => $user->id]));
        $response->assertStatus(403);

        // Can not view user create form
        $response = $this->actingAs($user)->get(route('admin.users.create'));
        $response->assertStatus(403);

        // Can not store user
        $post = [
            'name' => 'test-name',
            'email' => 'email@test.test',
            'password' => 'password',
            'role_id' => 2,
        ];
        $response = $this->actingAs($user)->post(route('admin.users.store', $post));
        $response->assertStatus(403);

        // Can not view user edit form
        $response = $this->actingAs($user)->get(route('admin.users.edit', ['user_id' => $user->id]));
        $response->assertStatus(403);

        // Can not update user
        $post = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ];
        $response = $this->actingAs($user)->put(route('admin.users.update', $post));
        $response->assertStatus(403);

        // Can not delete user
        $response = $this->actingAs($user)->get(route('admin.users.destroy', ['user_id' => $user->id]));
        $response->assertStatus(403);

        $user->delete();
        $role->delete();
    }
}
