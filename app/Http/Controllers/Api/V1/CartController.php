<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Http\Resources\Api\V1\CartCollection;
use App\Models\Cart;
use App\Models\Description;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
        
        $price = [];
        // $id = ''; 
        
        foreach ($cart as $cart_item) {
            $price[] = $cart_item->price;
        }

        $cart_instance = [
            'cart' =>  $cart,
            'total' => array_sum($price)
        ];

        return response()->json([
            'data' => $cart_instance
        ]);
        // return new CartCollection($cart_instance);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cart = Cart::where('id', $id)->get();
        return new CartCollection($cart);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart_instance = Cart::where('id', $id)->get();
        $cart = Cart::where('id', $id)->first();
        
        foreach ($cart_instance as $cart_item) {
            // return ApiResponse::successResponse($cart_item);
            $order = Order::where('id', $cart_item->order_id)->first();
            $order->delete();
            $description = Description::where('id', $cart_item->description_id)->first();
            $description->delete();
        }

        $cart->delete();
        NotificationController::Notify(Auth::id(), "You've deleted an item from your cart", Carbon::now(), 'red', 'deletion');
        return ApiResponse::successResponse('deleted');
    }
}
