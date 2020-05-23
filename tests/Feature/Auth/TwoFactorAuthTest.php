<?php

namespace Tests\Feature\Auth;

use App\Notifications\Auth\TwoFactorCodeEmail;
use App\User;
use Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TwoFactorAuthTest extends TestCase
{
    /**
     * Test auth user can open 2fa form
     */
    public function testAuthUserCanView2faForm()
    {
        $user = factory(User::class)->create([
            'two_factor_state' => 1,
            'two_factor_method' => 'email',
        ]);

        $response = $this->actingAs($user)->get(route('2fa.form'));
        $response->assertSuccessful();
        $response->assertViewIs('auth.two_factor');
        $user->delete();
    }

    /**
     * Test non auth user cannot view 2fa form
     */
    public function testNonAuthUserCannotView2faForm()
    {
        $response = $this->get(route('2fa.form'));
        $response->assertRedirect('/login');
    }

    /**
     * Test user can pass two factor auth
     */
    public function testUserCanPassTwoFactorAuth()
    {
        Notification::fake();

        $user = factory(User::class)->create([
            'two_factor_state' => 1,
            'two_factor_method' => 'email',
        ]);

        $response = $this->followingRedirects()->post(route('login'), [
                'email' => $user->email,
                'password' => 'password',
        ]);
        $response->assertSuccessful();
        $response->assertViewIs('auth.two_factor');

        $user->refresh();

        Notification::assertSentTo(
            $user,
            TwoFactorCodeEmail::class,
            function ($notification, $channels, $notifiable) use ($user) {
                return $notifiable->two_factor_code == $user->two_factor_code;
            }
        );

        $response = $this->actingAs($user)->post(route('2fa.check'), [
            'two_factor_code' => $user->two_factor_code,
        ]);

        $response->assertRedirect(route('users.profile'));
        $this->assertAuthenticatedAs($user);

        $user->delete();
    }

    /**
     * Test user can not pass two factor auth with wrong code
     */
    public function testUserCannotPassTwoFactorAuthWithWrongCode()
    {
        Notification::fake();

        $user = factory(User::class)->create([
            'two_factor_state' => 1,
            'two_factor_method' => 'email',
        ]);

        $response = $this->followingRedirects()->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertSuccessful();

        $user->refresh();

        $response = $this->actingAs($user)->from(route('2fa.form'))->post(route('2fa.check'), [
            'two_factor_code' => '123', // wrong code
        ]);
        $response->assertRedirect(route('2fa.form'));
        $response->assertSessionHasErrors('two_factor_code');
        $this->assertFalse(session()->hasOldInput('two_factor_code'));

        $user->delete();
    }

    /**
     * Test user can resend two factor code
     */
    public function testUserCanResendTwoFactorCode()
    {
        Notification::fake();

        $user = factory(User::class)->create([
            'two_factor_state' => 1,
            'two_factor_method' => 'email',
        ]);

        $response = $this->actingAs($user)->from(route('2fa.form'))->get(route('2fa.resend'));
        $response->assertRedirect(route('2fa.form'));
        $response->assertSessionHas('message');

        $user->refresh();

        Notification::assertSentTo(
            $user,
            TwoFactorCodeEmail::class,
            function ($notification, $channels, $notifiable) use ($user) {
                return $notifiable->two_factor_code == $user->two_factor_code;
            }
        );

        $user->delete();
    }

    /**
     * Test user can not pass two factor auth with time expired
     */
    public function testUserCannotPassTwoFactorAuthWithTimeExpired()
    {
        Notification::fake();

        $user = factory(User::class)->create([
            'two_factor_state' => 1,
            'two_factor_method' => 'email',
        ]);

        $response = $this->followingRedirects()->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertSuccessful();

        $user->refresh();
        $user->two_factor_expires_at = now()->subMinutes(1);
        $user->save();

        $response = $this->actingAs($user)->from(route('2fa.form'))->post(route('2fa.check'), [
            'two_factor_code' => $user->two_factor_code,
        ]);
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertGuest();

        $user->delete();
    }
}
