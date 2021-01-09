<?php

namespace Tests\Feature\Feature\Admin;

use App\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * Test authorized user can view dashboard
     *
     * @throws \Exception
     */
    public function testUserCanViewDashboard()
    {
        $user = factory(User::class)->create([
            'role_id' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertSuccessful();
        $response->assertViewIs('admin.dashboard');
        $user->delete();
    }

    /**
     * Test non auth user can not view dashboard
     *
     */
    public function testUserNonAuthCannotViewDashboard()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect('/login');
    }

    /**
     * Test non authorized user can not view dashboard
     *
     * @throws \Exception
     */
    public function testUserCannotViewDashboard()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
        $user->delete();
    }
}
