<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\HistoryCollection;
use App\Models\Cart;
use App\Models\Description;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function history ()
    {
        $cart = Cart::where('user_id', Auth::id())->latest()->get();
        
        $history = [];
        $id = ''; 

        foreach ($cart as $cart_item) {
            $order = Order::where('id', $cart_item->order_id)->first();

        // Create a new array for each $cart_item
        $historyItem = [
            'feature' => $cart_item->feature,
            'quantity' => $cart_item->quantity,
            'price' => $cart_item->price,
            'status' => $order->status,
        ];

        // Add the new array to the $history array
        $history[] = $historyItem;
        }

        return new HistoryCollection($history);
    }
}
