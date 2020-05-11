<?php

namespace Tests\Feature\Auth;

use App\User;
use Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * Test for open login form
     *
     * @return void
     */
    public function testLoginFormView()
    {
        $response = $this->get('/login');
        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    /**
     * Test auth user cannot view login form
     */
    public function testAuthUserCannotViewLoginForm()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/users/profile');
    }

    /**
     * Test user can login with correct credential
     */
    public function testUserCanLogin()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/users/profile');
        $this->assertAuthenticatedAs($user);

        $user->delete();
    }

    /**
     * Test user cannot login with incorrect credential
     */
    public function testUserCannotLogin()
    {
        $user = factory(User::class)->create();

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();

        $user->delete();
    }

    /**
     * Test remember user on login
     */
    public function testRememberMe()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => 'on',
        ]);

        $response->assertRedirect('/users/profile');
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        $this->assertAuthenticatedAs($user);

        $user->delete();
    }

    /**
     * Test auth user can logout
     */
    public function testAuthUserCanLogout()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();

        $user->delete();
    }
}
