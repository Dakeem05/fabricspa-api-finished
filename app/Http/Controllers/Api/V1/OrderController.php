<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Mail\DeliverReminderEmail;
use App\Mail\UnretievedReminderEmail;
use App\Models\Cart;
use App\Models\Checkout;
use App\Models\Description;
use App\Models\Feature;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all();

        $response = [];
        foreach ($orders as $order) {
            $email = User::where('id', $order->user_id)->first()->email;
            // $checkout = Checkout::where('id', $order->checkout_id)->first();
            // $description_id = $checkout->description_id;
            // $feature_name = [];
            // $quantity = [];
            // $price = [];
            // foreach ($description_id as $id) {
            //     $description = Description::where('id', $id)->first();
            //     $feature_name[] = Feature::where('id', $description->feature_id)->first()->name;
            //     $quantity[] = $description->amount;
            // }
            // foreach ($checkout->cart_id as $id) {
            //     $cart = Cart::where('id', $id)->first();
            //     $price[] = $cart->price;
            // }
            $status = $order->status;

            $response[] = [
                'email' => $email,
                'id' => $order->id,
                // 's'
                // 'feature_name' => $feature_name,
                // 'quantity' => $quantity,
                'status' => $status,
                // 'price' => $price,
            ];
        }

        return response()->json($response);
    }

//     public function index()
// {
//     $orders = Order::with(['user', 'checkout'])
//         ->where('user_id', Auth::id())
//         ->get();

//     $response = [];

//     foreach ($orders as $order) {
//         $username = $order->user->username;

//         foreach ($order->checkout->description as $description) {
//             $feature_name = $description->feature->name;
//             $quantity = $description->amount;
//         }

//         foreach ($order->checkout->cart as $cart) {
//             $price = $cart->price;
//         }

//         $status = $order->status;

//         $response[] = [
//             'username' => $username,
//             'feature_name' => $feature_name,
//             'quantity' => $quantity,
//             'status' => $status,
//             'price' => $price,
//         ];
//     }

//     return response()->json($response);
// }


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
        $order = Order::where('id', $id)->first();

        $username = User::where('id', $order->user_id)->first();
        $checkout = Checkout::where('id', $order->checkout_id)->first();
        $description_id = $checkout->description_id;
        $descriptions = [];
        $carts = [];
        $featuress = [];
        $feature_name = '';
        $quantity = '';
        $price = '';
        foreach ($description_id as $id) {
            $description = Description::where('id', $id)->first();
            $descriptions[] = $description;
            // return $id;
            $features[] = Feature::where('id', $description->feature_id)->first();
            // $feature_name = Feature::where('id', $description->feature_id)->first()->name;
            // $quantity = $description->amount;
        }
        foreach ($checkout->cart_id as $id) {
            $cart = Cart::where('id', $id)->first();
            $carts[] = $cart;
            // $price = $cart->price;
        }
        $status = $order->status;

        $response[] = [
            'user' => $username,
            'description' => $descriptions,
            'cart' => $carts,
            'feature' => $features,
            'id' => $order->id,
            // 'feature_name' => $feature_name,
            // 'quantity' => $quantity,
            'status' => $status,
            // 'price' => $price,
        ];
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function deliverReminder(string $id)
    {
        $order = Order::where('status', 'paid')->where('id', $id)->first();
        // return $order;
        // foreach ($orders as $order) {
            $user = User::where('id', $order->user_id)->first();
            $count = order::where('status', 'paid')->where('user_id', $order->user_id)->count();
            Mail::to($user->email)->send(new DeliverReminderEmail($count, $user->last_name));
        // }
    }
    public function markDelivered(string $id)
    {
        $order = Order::where('status', 'paid')->where('id', $id)->first();
        // return $order;
        // foreach ($orders as $order) {
            $order->update(['status'=>'delivered']);
                 NotificationController::Notify($order->user_id, "Your order has been delivered please check your email, history and notifications regularly as we will write you when your order has been completed.", Carbon::now(), 'success', 'order');
        // }
    }
    public function markRetrieved(string $id)
    {
        $order = Order::where('status', 'completed')->where('id', $id)->first();
        // return $order;
        // foreach ($orders as $order) {
            $order->update(['status'=>'picked']);
            NotificationController::Notify($order->user_id, "You have picked up your order. Thanks for patronage", Carbon::now(), 'success', 'order');
        // }
    }
    public function completeOrder(string $id)
    {
        $order = Order::where('status', 'delivered')->where('id', $id)->first();
        // return $order;
        // foreach ($orders as $order) {
            $order->update(['status'=>'completed']);
              NotificationController::Notify($order->user_id, "Your order has been completed so please do well to come and pick it up", Carbon::now(), 'success', 'order');
        // }
    }
    public function retrieveReminder(string $id)
    {
        $order = Order::where('status', 'completed')->where('id', $id)->first();
        // return $order;
        // foreach ($orders as $order) {
            // $order->update(['status'=>'completed']);
            // foreach ($orders as $order) {
                $user = User::where('id', $order->user_id)->first();
                $count = order::where('status', 'completed')->where('user_id', $order->user_id)->count();
                Mail::to($user->email)->send(new UnretievedReminderEmail($count, $user->last_name));
        // }
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
        //
    }
}
