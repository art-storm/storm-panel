<?php

namespace Tests\Feature\Auth;

use App\Models\EmailChanges;
use App\Notifications\EmailChange;
use App\User;
use Faker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Http\Controllers\Auth\ChangeEmailController as ChangeEmailController;

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
        $response = $this->actingAs($user)->get(route('email.change.form'));
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
        $response = $this->get(route('email.change.form'));
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
        $response = $this->actingAs($user)->from(route('email.change.form'))
            ->post(route('email.change'), [
                'email' => $emailNew,
                'password' => 'password',
            ]);
        $response->assertRedirect(route('email.change.form'));
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
        $response = $this->actingAs($user)->from(route('email.change.form'))
            ->post(route('email.change'), [
                'email' => $emailNew,
                'password' => 'password-wrong',
            ]);
        $response->assertRedirect(route('email.change.form'));
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
        $response = $this->actingAs($user)->from(route('email.change.form'))
            ->post(route('email.change'), [
                'email' => $emailNew,
                'password' => 'password',
            ]);
        $response->assertRedirect(route('email.change.notify'));

        $dbEmailChanges = EmailChanges::where('email', '=', $emailOld)->first();
        $this->assertNotNull($dbEmailChanges);

        Notification::assertSentTo(
            $user,
            EmailChange::class,
            function ($notification, $channels) use ($dbEmailChanges) {
                return $notification->token === $dbEmailChanges->change_code;
            }
        );

        $this->get(route('email.change.confirm', ['token' => $dbEmailChanges->change_code]));

        $user->refresh();
        $this->assertTrue($user->email === $emailNew);

        $user->delete();
    }
}
