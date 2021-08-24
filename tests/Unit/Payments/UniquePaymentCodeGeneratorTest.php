<?php

namespace Tests\Unit\Payments;

use App\Payments\UniquePaymentCodeGenerator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Payment;
use App\Models\User;
use App\Payments\FakePaymentCodeGenerator;
use App\Payments\PaymentCodeGenerator;
use Illuminate\Auth\AuthenticationException;

class UniquePaymentCodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function code_must_be_16_chars_long() {
        $generator = new UniquePaymentCodeGenerator();
        $code = $generator->generate();

        $this->assertEquals(16, strlen($code));

    }

    /** @test */
    public function code_must_have_only_letters_and_numbers() {
        $generator = new UniquePaymentCodeGenerator();
        $code = $generator->generate();

        $this->assertMatchesRegularExpression('/^[A-Z,0-9]*$/', $code);

    }

    /** @test */
    public function code_must_be_unique() {

        $codes = collect();
        for ($i=0; $i < 1000; $i++) {
            $codes->push((new UniquePaymentCodeGenerator())->generate());
        }

        //comparing COLLECTIONS "codes counter" and "unique code counter"
        $this->assertEquals($codes->count(), $codes->unique()->count());
    }

}

