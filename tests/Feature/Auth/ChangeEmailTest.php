<?php

namespace Tests\Feature\Auth;

use App\Models\EmailChanges;
use App\Notifications\EmailChange;
use App\User;
use Faker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ChangeEmailTest extends TestCase
{
    /**
     * Test auth user can view change email form
     *
     * @return void
     */
    public function testAuthUserCanViewChangeEmailForm()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get(route('email_changeForm'));
        $response->assertSuccessful();
        $response->assertViewIs('users.email_change');
    }

    /**
     * Test not auth user can not open change email form
     *
     * @return void
     */
    public function testNonAuthUserCannotViewChangeEmailForm()
    {
        $response = $this->get(route('email_changeForm'));
        $response->assertRedirect('/login');
    }

    /**
     * Test user input invalid email
     *
     * @return void
     */
    public function testUserInputInvalidEmail()
    {
        $emailNew = 'dfghdgshdgjhgf';
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->from(route('email_changeForm'))
            ->post(route('email_change'), [
                'email' => $emailNew,
                'password' => 'password',
            ]);
        $response->assertRedirect(route('email_changeForm'));
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test user input wrong password
     *
     * @return void
     */
    public function testUserInputWrongPassword()
    {
        $emailNew = Faker\Factory::create()->unique()->email;

        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->from(route('email_changeForm'))
            ->post(route('email_change'), [
                'email' => $emailNew,
                'password' => 'password-wrong',
            ]);
        $response->assertRedirect(route('email_changeForm'));
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('email'));
    }

    /**
     * Test user can change email
     *
     * @throws \Exception
     * @return void
     */
    public function testUserCanChangeEmail()
    {
        Notification::fake();

        $user = factory(User::class)->create();
        $emailOld = $user->email;
        $emailNew = Faker\Factory::create()->unique()->safeEmail;
        $response = $this->actingAs($user)->from(route('email_changeForm'))
            ->post(route('email_change'), [
                'email' => $emailNew,
                'password' => 'password',
            ]);
        $response->assertRedirect(route('email_change_notify'));

        $dbEmailChanges = EmailChanges::where('email', '=', $emailOld)->first();
        $this->assertNotNull($dbEmailChanges);

        Notification::assertSentTo(
            $user,
            EmailChange::class,
            function ($notification, $channels) use ($dbEmailChanges) {
                return $notification->token === $dbEmailChanges->change_code;
            }
        );

        $this->get(route('email_change_confirm', ['token' => $dbEmailChanges->change_code]));

        $user->refresh();
        $this->assertTrue($user->email === $emailNew);

        $user->delete();
    }
}
