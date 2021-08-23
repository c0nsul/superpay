<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentsController extends Controller
{

    public function create()
    {
        return view("payments.create");
    }


    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|integer|min:100',
            'currency' => 'required',
            'name' => 'required',
        ]);

        $request->user()->payments()->create([
            'amount' => $request->amount,
            'currency' => $request->currency,
            'email' => $request->email,
            'name' => $request->name,
            'description' => $request->description,
            'message' => $request->message,
        ]);
    }
}
