<?php

namespace Tests\Feature\Auth;

use App\User;
use Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    /**
     * Test auth user can view change password form
     *
     * @return void
     */
    public function testAuthUserCanViewChangePasswordForm()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get(route('password_changeForm'));
        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.change');
    }

    /**
     * Test not auth user can not open change password form
     *
     * @return void
     */
    public function testNonAuthUserCannotViewChangePasswordForm()
    {
        $response = $this->get(route('password_changeForm'));
        $response->assertRedirect('/login');
    }

    /**
     * Test user input wrong password
     *
     * @return void
     */
    public function testUserInputWrongPassword()
    {
        $passwordNew = 'password_new';
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->from(route('password_changeForm'))
            ->post(route('password_change'), [
                'password_current' => 'password-wrong',
                'password' => $passwordNew,
                'password_confirmation' => $passwordNew,
            ]);
        $response->assertRedirect(route('password_changeForm'));
        $response->assertSessionHasErrors('password_current');
    }

    /**
     * Test user input invalid new password
     *
     * @return void
     */
    public function testUserInputInvalidNewPassword()
    {
        $passwordNew = 'pwd';
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->from(route('password_changeForm'))
            ->post(route('password_change'), [
                'password_current' => 'password',
                'password' => $passwordNew,
                'password_confirmation' => $passwordNew,
            ]);
        $response->assertRedirect(route('password_changeForm'));
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test user input wrong password confirmation
     *
     * @return void
     */
    public function testUserInputWrongPasswordConfirmation()
    {
        $passwordNew = 'password_new';
        $passwordConfirmation = 'password_confirmation';
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->from(route('password_changeForm'))
            ->post(route('password_change'), [
                'password_current' => 'password',
                'password' => $passwordNew,
                'password_confirmation' => $passwordConfirmation,
            ]);
        $response->assertRedirect(route('password_changeForm'));
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test user can change password
     *
     * @throws \Exception
     * @return void
     */
    public function testUserCanChangePassword()
    {
        $passwordNew = 'password_new';
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->from(route('password_changeForm'))
            ->post(route('password_change'), [
                'password_current' => 'password',
                'password' => $passwordNew,
                'password_confirmation' => $passwordNew,
            ]);
        $response->assertRedirect(route('users_profile'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check($passwordNew, $user->password));
        $user->delete();
    }
}
