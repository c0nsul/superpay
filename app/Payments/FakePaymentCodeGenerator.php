<?php

namespace App\Payments;

class FakePaymentCodeGenerator implements PaymentCodeGenerator
{
    /**
     * @throws \Exception
     */
    public function generate(){
        //throw  new \Exception('Method  generate() is not implemented');
        return 'TESTCODE123';
    }
}
