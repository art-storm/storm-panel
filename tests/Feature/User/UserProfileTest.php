<?php

namespace Tests\Feature\User;

use App\User;
use Faker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    /**
     * Test user can view profile
     *
     * @return void
     */
    public function testUserCanViewProfile()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get(route('users.profile'));
        $response->assertSuccessful();
        $response->assertViewIs('users.profile');
    }

    /**
     * Test non auth user can not view profile
     *
     * @return void
     */
    public function testUserNonAuthCannotViewProfile()
    {
        $response = $this->get(route('users.profile'));
        $response->assertRedirect('/login');
    }

    /**
     * Test user cannot change name with invalid data
     */
    public function testUserCannotChangeNameWithInvalidData()
    {
        $user = factory(User::class)->make();

        $response = $this->actingAs($user)->from(route('users.profile'))
            ->post(route('users.profile_update'), [
                'name' => '',
        ]);

        $response->assertRedirect(route('users.profile'));
        $response->assertSessionHasErrors('name');

        $response = $this->actingAs($user)->from(route('users.profile'))
            ->post(route('users.profile_update'), [
                'name' => Str::random(40),
        ]);

        $response->assertRedirect(route('users.profile'));
        $response->assertSessionHasErrors('name');
        $this->assertTrue(session()->hasOldInput('name'));
    }

    /**
     * Test user can change name in profile
     */
    public function testUserCanChangeName()
    {
        $user = factory(User::class)->create();

        $nameNew = Faker\Factory::create()->name;

        $response = $this->actingAs($user)->from(route('users.profile'))
            ->post(route('users.profile_update'), [
                'name' => $nameNew,
            ]);

        $response->assertRedirect(route('users.profile'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue($user->name === $nameNew);
        $user->delete();
    }
}
