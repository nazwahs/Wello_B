<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:bank_transfer,e_wallet,credit_card',
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => $validated['payment_method'],
            'amount' => $order->total,
            'payment_status' => 'pending',
            'paid_at' => null,
        ]);

        return response()->json([
            'message' => 'Payment created successfully',
            'data' => $payment
        ], 201);
    }

    public function show(string $id)
    {
        $payment = Payment::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('order_id', $id)->firstOrFail();

        return response()->json([
            'data' => $payment
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}