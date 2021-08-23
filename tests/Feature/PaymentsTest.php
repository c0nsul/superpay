<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

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


}
