<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'address', 'courier', 'payment', 'trackings'])
            ->get();

        return response()->json([
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        $order = Order::where('user_id', auth()->id())
            ->with(['items.product', 'address', 'courier', 'payment', 'trackings'])
            ->findOrFail($id);

        return response()->json([
            'data' => $order
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