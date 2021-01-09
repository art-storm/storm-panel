<?php

namespace Tests\Feature\Auth;

use App\Notifications\Auth\EmailConfirm;
use App\User;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str as Str;
use Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    /**
     * Test for open password reset form
     */
    public function testForgotPasswordFormView()
    {
        $response = $this->get('/password/reset');
        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.email');
    }

    /**
     * est user cannot receive link with invalid email
     */
    public function testUserSubmitRequestWithInvalidEmail()
    {
        $response = $this->from('/password/reset')->withSession(['_token' => 'test'])->post('/password/email', [
            'email' => Str::random(20),
            '_token' => 'test',
        ]);

        $response->assertRedirect('/password/reset');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertGuest();
    }

    /**
     * Test user cannot receive link with incorrect credential
     */
    public function testUserCannotReceiveLink()
    {
        $response = $this->from('/password/reset')->withSession(['_token' => 'test'])->post('/password/email', [
            'email' => 'wrong-email@wrong-email.wrong',
            '_token' => 'test',
        ]);

        $response->assertRedirect('/password/reset');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertGuest();
    }

    /**
     * Test non activate user receive email with activate link.
     *
     */
    public function testUserNonActivateReceiveEmailWithActivateLink()
    {
        Notification::fake();

        $user = factory(User::class)->create([
            'is_activate' => 0,
        ]);

        $response = $this->from('/password/reset')->withSession(['_token' => 'test'])->post('/password/email', [
            'email' => $user->email,
            '_token' => 'test',
        ]);

        $response->assertRedirect(route('register.verify'));

        $dbUser = User::where('email', '=', $user->email)
            ->where('is_activate', '=', 0)
            ->first();

        Notification::assertSentTo(
            $dbUser,
            EmailConfirm::class,
            function ($notification, $channels) use ($dbUser) {
                return $notification->user->activation_code === $dbUser->activation_code;
            }
        );

        $user->delete();
    }

    /**
     * Test activate user receive email with reset link.
     *
     */
    public function testUserActivateReceiveEmailWithResetLink()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $response = $this->from('/password/reset')->withSession(['_token' => 'test'])->post('/password/email', [
            'email' => $user->email,
            '_token' => 'test',
        ]);

        $dbPasswordReset = DB::table('password_resets')
            ->where('email', '=', $user->email)
            ->first();
        $this->assertNotNull($dbPasswordReset);

        $dbUser = User::where('email', '=', $user->email)
            ->where('is_activate', '=', 1)
            ->first();

        $response->assertRedirect('/password/reset');

        Notification::assertSentTo(
            $dbUser,
            ResetPassword::class,
            function ($notification, $channels) use ($dbPasswordReset) {
                return Hash::check($notification->token, $dbPasswordReset->token) === true;
            }
        );

        $response->assertRedirect('/password/reset');
        $response->assertSessionHas('status');
        $this->assertGuest();

        DB::table('password_resets')
            ->where('email', '=', $user->email)
            ->delete();
        $user->delete();
    }

    /**
     * Test activate user cannot reset password with not valid password.
     *
     */
    public function testUserActivateCannotResetPassword()
    {
        $user = factory(User::class)->create();

        $token = Password::broker()->createToken($user);

        $password = 'pwd';
        $passwordConfirm = 'pwd_confirm';

        $response = $this->from(route('password.reset', ['token' => $token]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $passwordConfirm,
            ]);
        $response->assertRedirect(route('password.reset', ['token' => $token]));
        $response->assertSessionHasErrors('password');
        $this->assertGuest();

        $user->refresh();
        $this->assertFalse(Hash::check($password, $user->password));

        Password::broker()->deleteToken($user);
        $user->delete();
    }

    /**
     * Test activate user can reset password.
     *
     */
    public function testUserActivateCanResetPassword()
    {
        $user = factory(User::class)->create();

        $token = Password::broker()->createToken($user);

        $password = 'password_new';

        $this->followingRedirects()
            ->from(route('password.reset', [
                'token' => $token,
            ]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertSuccessful()
            ->assertViewIs('auth.login');

        $this->assertGuest();

        $user->refresh();
        $this->assertTrue(Hash::check($password, $user->password));

        Password::broker()->deleteToken($user);
        $user->delete();
    }
}
