<?php

namespace Tests\Feature\Auth;

use App\Notifications\Auth\EmailConfirm;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    /**
     * Test for open registration form
     *
     * @return void
     */
    public function testRegistrationFormView()
    {
        $response = $this->get('/register');
        $response->assertSuccessful();
        $response->assertViewIs('auth.register');
    }

    /**
     * Test auth user cannot view registration form
     */
    public function testAuthUserCannotViewRegistrationForm()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect('/users/profile');
    }

    /**
     * Test user cannot register with incorrect credential
     */
    public function testUserCannotRegister()
    {
        $user = factory(User::class)->make();

        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrors('password');
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(session()->hasOldInput('password_confirmation'));
        $this->assertGuest();

        $response = $this->from('/register')->post('/register', [
            'name' => $user->name,
            'email' => 'no-valid-email',
            'password' => 'password',
            'password_confirmation' => 'wrong-confirmation',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(session()->hasOldInput('password_confirmation'));
        $this->assertGuest();
    }

    /**
     * Test user can register with correct data and activate
     */
    public function testUserCanRegisterAndActivate()
    {
        Notification::fake();

        $user = factory(User::class)->make();

        $post = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->from('/register')->post('/register', $post);
        $response->assertRedirect(route('register_verify'));

        $dbUser = User::where('email', '=', $user->email)
            ->where('is_activate', '=', 0)
            ->first();
        $this->assertNotNull($dbUser);

        Notification::assertSentTo(
            $dbUser,
            EmailConfirm::class,
            function ($notification, $channels) use ($dbUser) {
                return $notification->user->activation_code === $dbUser->activation_code;
            }
        );

        $user->id = $dbUser->id;

        $response = $this->get(route('activate_user', ['code' => $dbUser->activation_code ]));
        $response->assertRedirect(route('users_welcome'));
        $this->assertAuthenticatedAs($user);

        $dbUser = User::where('email', '=', $user->email)
            ->where('is_activate', '=', 1)
            ->where('activation_code', '=', null)
            ->first();
        $this->assertNotNull($dbUser);

        $dbUser->delete();
    }
}
