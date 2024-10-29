<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'success' => false,
                'msg' => $errors,
            ], 422);
        }

        $product = Product::find($request->input('product_id'));

        if ($product->stock < $request->input('qty')) {
            return response()->json(['success' => false, 'msg' => 'Insufficient stock'], 400);
        }

        $product->stock -= $request->input('qty');
        $product->save();

        $order = Order::create([
            'user_id' => $request->user->sub,
            'product_id' => $request->input('product_id'),
            'qty' => $request->input('qty'),
            'status' => 'pending',
        ]);

        return response()->json(['success' => true, 'msg' => 'Order created successfully', 'order' => $order], 201);
    }

    public function getOrderById(Request $request, $id)
    {
        $order = Order::with('product')->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'msg' => 'Order not found'], 404);
        }

        if ($order->user_id !== $request->user->sub) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        return response()->json(['success' => true, 'order' => $order]);
    }


    public function getAllOrderByUser(Request $request)
    {
        $orders = Order::where('user_id', $request->user->sub)->with('product')->get();

        return response()->json(['success' => true, 'orders' => $orders]);
    }
}