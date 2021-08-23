<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use App\Payments\FakePaymentCodeGenerator;
use App\Payments\PaymentCodeGenerator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function not_authenticated_users_cant_create_new_invoice()
    {
        //error details
        $this->withoutExceptionHandling([AuthenticationException::class]);

        //user creation
        $user = User::factory()->create();

        //routes and form
        $response = $this->get('payments/new');

        $response->assertStatus(302)->assertRedirect('login');
    }

    /** @test */
    public function customer_can_see_form_for_creating_new_invoice()
    {
        //error details
        $this->withoutExceptionHandling();

        //user creation
        $user = User::factory()->create();

        //routes and form
        $this->actingAs($user)->get('payments/new')
            ->assertStatus(200)
            ->assertSee("Create new Invoice");
    }

    /** @test */
    public function auth_user_can_create_new_payment()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();


        $response = $this->actingAs($user)->json('post', "payments", [
            'email' => 'test@mail.com',
            'amount' => '5000',
            'currency' => 'usd',
            'name' => 'Tob Bradly',
            'description' => 'payment desc',
            'message' => 'Hello',
        ]);

        $response->assertStatus(200);

        $this->assertEquals(1, Payment::count());

        tap(
            Payment::first(), function ($payment) use ($user) {
            $this->assertEquals($user->id, $payment->user_id);
            $this->assertEquals('test@mail.com', $payment->email);
            $this->assertEquals('5000', $payment->amount);
            $this->assertEquals('usd', $payment->currency);
            $this->assertEquals('Tob Bradly', $payment->name);
            $this->assertEquals('Hello', $payment->message);
            $this->assertEquals('payment desc', $payment->description);
        });
    }

    /** @test */
    public function not_auth_user_can_create_new_payment()
    {

        $response = $this->json('post', "payments", [
            'email' => 'test@mail.com',
            'amount' => '5000',
            'currency' => 'usd',
            'name' => 'Tob Bradly',
            'description' => 'payment desc',
            'message' => 'Hello',
        ]);

        $response->assertStatus(401);

        $this->assertEquals(0, Payment::count());
    }


    /** @test */
    public function email_field_is_required_to_create_payment()
    {

        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('post', "payments", [
            //no 'email' => 'test@mail.com',
            'amount' => '5000',
            'currency' => 'usd',
            'name' => 'Tob Bradly',
            'description' => 'payment desc',
            'message' => 'Hello',
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, Payment::count());
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function email_field_should_be_valid_to_create_payment()
    {

        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('post', "payments", [
            'email' => 'not-valid-email',
            'amount' => '5000',
            'currency' => 'usd',
            'name' => 'Tob Bradly',
            'description' => 'payment desc',
            'message' => 'Hello',
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, Payment::count());
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function amount_field_is_required_to_create_payment()
    {

        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('post', "payments", [
            'email' => 'test@mail.com',
            //'amount' => '5000',
            'currency' => 'usd',
            'name' => 'Tob Bradly',
            'description' => 'payment desc',
            'message' => 'Hello',
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, Payment::count());
        $response->assertJsonValidationErrors('amount');
    }

    /** @test */
    public function amount_field_should_be_integer_to_create_payment()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('post', "payments", [
            'email' => 'test@mail.com',
            'amount' => 'some-amount', //incorrect
            'currency' => 'usd',
            'name' => 'Tob Bradly',
            'description' => 'payment desc',
            'message' => 'Hello',
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, Payment::count());
        $response->assertJsonValidationErrors('amount');
    }

    /** @test */
    public function code_field_is_required_to_create_payment()
    {

        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $fakePaymentCodeGenerator = new FakePaymentCodeGenerator();
        $this->app->instance(PaymentCodeGenerator::class, $fakePaymentCodeGenerator);

        $response = $this->actingAs($user)->json('post', "payments", [
            'email' => 'test@mail.com',
            'amount' => '5000',
            'currency' => 'usd',
            'name' => 'Tob Bradly',
            'description' => 'payment desc',
            'message' => 'Hello',
        ]);

        $response->assertStatus(200);

        $this->assertEquals(1, Payment::count());

        tap(
            Payment::first(), function ($payment) use ($user) {
            $this->assertEquals($user->id, $payment->user_id);
            $this->assertEquals('test@mail.com', $payment->email);
            $this->assertEquals('5000', $payment->amount);
            $this->assertEquals('usd', $payment->currency);
            $this->assertEquals('Tob Bradly', $payment->name);
            $this->assertEquals('Hello', $payment->message);
            $this->assertEquals('payment desc', $payment->description);
            $this->assertEquals('TESTCODE123', $payment->code);
        });
    }

}
