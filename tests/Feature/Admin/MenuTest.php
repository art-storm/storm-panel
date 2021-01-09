<?php

namespace Tests\Feature\Admin;

use App\Models\MenuItem;
use App\Traits\Tests\RoleTrait;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RoleTrait;

    private $admin;

    // Permission for admin login
    private $permission_id = 5;

    // Permissions for menu view/update
    private $permissions = [14, 15];

    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
        $this->admin = Auth::user();
    }

    /**
     * Test authorized user can view menus list
     */
    public function testUserCanViewMenuList()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.menus.index'));
        $response->assertSuccessful();
        $response->assertViewIs('admin.menus');
    }

    /**
     * Test authorized user can view menu edit page
     */
    public function testUserCanViewMenuEditPage()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.menus.edit', ['menu_id' => 1]));
        $response->assertSuccessful();
        $response->assertViewIs('admin.menus_edit');
    }

    /**
     * Test authorized user can view menu item create form
     */
    public function testUserCanViewMenuItemCreateForm()
    {
        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.menuitems.create', ['menu_id' => 1]));
        $response->assertSuccessful();
        $response->assertViewIs('admin.menus_item_create');
    }

    /**
     * Test authorized user can create menu item
     */
    public function testUserCanCreateMenuItem()
    {
        $user = $this->admin;

        $post = [
            'menu_id' => 1,
            'title' => 'title_test',
            'url' => '/url_test',
            'target' => '_self',
            'icon_class' => 'icon_class_test',
            'color' => '#000000',
            'divider' => '1',
        ];

        $response = $this->actingAs($user)->post(route('admin.menuitems.store'), $post);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.menus.edit', ['menu_id' => $post['menu_id']]));
        $response->assertSessionHas('status');

        $item_db = MenuItem::where('title', '=', $post['title'])->first();
        $this->assertTrue($item_db->menu_id == $post['menu_id']);
        $this->assertTrue($item_db->url == $post['url']);
        $this->assertTrue($item_db->target == $post['target']);
        $this->assertTrue($item_db->icon_class == $post['icon_class']);
        $this->assertTrue($item_db->color == $post['color']);
        $this->assertTrue($item_db->divider == $post['divider']);
        $item_db->delete();
    }

    /**
     * Test authorized user can view menu item edit form
     */
    public function testUserCanViewMenuItemEditForm()
    {
        $user = $this->admin;

        $item = factory(MenuItem::class)->create();

        $response = $this->actingAs($user)->get(route('admin.menuitems.edit', ['item_id' => $item->id]));
        $response->assertSuccessful();
        $response->assertViewIs('admin.menus_item_edit');
        $item->delete();
    }

    /**
     * Test authorized user can update menu item
     */
    public function testUserCanUpdateMenuItem()
    {
        $item = factory(MenuItem::class)->create();

        $post = [
            'item_id' => $item->id,
            'title' => 'title-update-test',
            'url' => '/url-update-test',
            'target' => '_blank',
            'icon_class' => 'icon_class_update_test',
            'color' => '#FFFFFF',
            'divider' => '',
        ];

        $user = $this->admin;
        $response = $this->actingAs($user)->put(route('admin.menuitems.update', $post));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.menus.edit', ['menu_id' => $item->menu_id]));
        $response->assertSessionHas('status');

        $item_db = MenuItem::find($item->id);
        $this->assertTrue($item_db->title == $post['title']);
        $this->assertTrue($item_db->url == $post['url']);
        $this->assertTrue($item_db->target == $post['target']);
        $this->assertTrue($item_db->icon_class == $post['icon_class']);
        $this->assertTrue($item_db->color == $post['color']);
        $this->assertTrue($item_db->divider == $post['divider']);
        $item_db->delete();
    }

    /**
     * Test authorized user can delete menu item
     */
    public function testUserCanDeleteMenuItem()
    {
        $item = factory(MenuItem::class)->create();
        $itemChildren = factory(MenuItem::class)->create([
            'parent_id' => $item->id,
        ]);

        $user = $this->admin;
        $response = $this->actingAs($user)->get(route('admin.menuitems.destroy', ['item_id' => $item->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.menus.edit', ['menu_id' => $item->menu_id]));
        $response->assertSessionHas('status');

        $item_db = MenuItem::find($item->id);
        $this->assertNull($item_db);

        $itemChildren_db = MenuItem::find($itemChildren->id);
        $this->assertNull($itemChildren_db);
    }

    /**
     * Test permissions for authorized user
     */
    public function testPermissionForUserAuthorized()
    {
        $menu_id = 1;

        $role = $this->createRole($this->permissions);
        $user = factory(User::class)->create([
            'role_id' => $role->id,
        ]);
        $item = factory(MenuItem::class)->create();

        // Can view menus list
        $response = $this->actingAs($user)->get(route('admin.menus.index'));
        $response->assertSuccessful();

        // Can view menu edit page
        $response = $this->actingAs($user)->get(route('admin.menus.edit', ['menu_id' => $menu_id]));
        $response->assertSuccessful();

        // Can view menu item create form
        $response = $this->actingAs($user)->get(route('admin.menuitems.create', ['menu_id' => $menu_id]));
        $response->assertSuccessful();

        // Can store menu item
        $post = [
            'menu_id' => $menu_id,
            'title' => 'title_test',
            'url' => '/url_test',
            'target' => '_self',
            'icon_class' => 'icon_class_test',
            'color' => '#000000',
            'divider' => '1',
        ];
        $response = $this->actingAs($user)->post(route('admin.menuitems.store'), $post);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.menus.edit', ['menu_id' => $post['menu_id']]));
        MenuItem::where('title', '=', $post['title'])->delete();

        // Can view menu item edit form
        $response = $this->actingAs($user)->get(route('admin.menuitems.edit', ['item_id' => $item->id]));
        $response->assertSuccessful();

        // Can update menu item
        $post = [
            'item_id' => $item->id,
            'title' => 'title-update-test',
            'url' => '/url-update-test',
            'target' => '_blank',
            'icon_class' => 'icon_class_update_test',
            'color' => '#FFFFFF',
            'divider' => '',
        ];
        $response = $this->actingAs($user)->put(route('admin.menuitems.update', $post));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.menus.edit', ['menu_id' => $item->menu_id]));

        // Can delete menu item
        $response = $this->actingAs($user)->get(route('admin.menuitems.destroy', ['item_id' => $item->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.menus.edit', ['menu_id' => $item->menu_id]));

        $user->delete();
        $role->delete();
        $item->delete();
    }

    /**
     * Test permissions for non authorized user
     */
    public function testPermissionForUserNonAuthorized()
    {
        $menu_id = 1;

        $role = $this->createRole();
        $user = factory(User::class)->create([
            'role_id' => $role->id,
        ]);
        $item = factory(MenuItem::class)->create();

        // Can not view menus list
        $response = $this->actingAs($user)->get(route('admin.menus.index'));
        $response->assertStatus(403);

        // Can not view menu edit page
        $response = $this->actingAs($user)->get(route('admin.menus.edit', ['menu_id' => $menu_id]));
        $response->assertStatus(403);

        // Can not view menu item create form
        $response = $this->actingAs($user)->get(route('admin.menuitems.create', ['menu_id' => $menu_id]));
        $response->assertStatus(403);

        // Can not store menu item
        $post = [
            'menu_id' => $menu_id,
            'title' => 'title_test',
            'url' => '/url_test',
            'target' => '_self',
            'icon_class' => 'icon_class_test',
            'color' => '#000000',
            'divider' => '1',
        ];
        $response = $this->actingAs($user)->post(route('admin.menuitems.store'), $post);
        $response->assertStatus(403);

        // Can not view menu item edit form
        $response = $this->actingAs($user)->get(route('admin.menuitems.edit', ['item_id' => $item->id]));
        $response->assertStatus(403);

        // Can not update menu item
        $post = [
            'item_id' => $item->id,
            'title' => 'title-update-test',
            'url' => '/url-update-test',
            'target' => '_blank',
            'icon_class' => 'icon_class_update_test',
            'color' => '#FFFFFF',
            'divider' => '',
        ];
        $response = $this->actingAs($user)->put(route('admin.menuitems.update', $post));
        $response->assertStatus(403);

        // Can not delete menu item
        $response = $this->actingAs($user)->get(route('admin.menuitems.destroy', ['item_id' => $item->id]));
        $response->assertStatus(403);

        $user->delete();
        $role->delete();
        $item->delete();
    }
}
