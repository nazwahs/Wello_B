<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())
            ->with('items.product')
            ->first();

        if (!$cart) {
            return response()->json([
                'data' => [
                    'items' => [],
                    'total' => 0
                ]
            ]);
        }

        $total = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'data' => [
                'id' => $cart->id,
                'items' => $cart->items,
                'total' => $total
            ]
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
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