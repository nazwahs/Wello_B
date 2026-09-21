<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'courier_id' => 'required|exists:couriers,id',
            'payment_method' => 'required|in:bank_transfer,e_wallet,cod',
            'delivery_schedule' => 'required|date',
            'voucher_code' => 'nullable|string',
        ]);

        $address = Address::where('id', $validated['address_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $cart = Cart::where('user_id', auth()->id())
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;

            foreach ($cart->items as $item) {
                $subtotal += $item->product->price * $item->quantity;
            }

            $shippingCost = 0;
            $discount = 0;
            $total = $subtotal + $shippingCost - $discount;

            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => $address->id,
                'courier_id' => $validated['courier_id'],
                'shipping_rate_id' => null,
                'order_number' => 'WELLO-' . strtoupper(Str::random(8)),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'delivery_schedule' => $validated['delivery_schedule'],
                'status' => 'waiting_payment',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->product->price * $item->quantity,
                ]);
            }

            $cart->items()->delete();

            DB::commit();

            $order->load([
                'items.product',
                'address',
                'courier'
            ]);

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
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