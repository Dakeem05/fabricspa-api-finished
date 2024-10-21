<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Http\Resources\Api\V1\CheckoutCollection;
use App\Models\Cart;
use App\Models\Checkout;
use App\Models\Description;
use App\Models\Feature;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Type;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Unicodeveloper\Paystack\Paystack;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function returnCart()
    {
        $id = Auth::id();

        $cart = Cart::where('user_id', $id)->get();
        $totalPrice = [];
        foreach ($cart as $cartItem) {
            $slashed_price = $cartItem->slashed_price;
            $price = $cartItem->price;
            
            if($slashed_price == null) {
                $totalPrice[] = $price;
            } else{
                $totalPrice[] = $slashed_price;
            }
        }
        
        $user = User::where('id', $id)->first();

        $user->update([
            'cart_total' => array_sum($totalPrice)
        ]);
        
        return response()->json([
            'cart' => $cart,
            'total_price' => array_sum($totalPrice)
        ]);
        // return new CheckoutCollection($cart);
        // return $cart;
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
    // public function store(Request $request)
    // {
    //     $rules = [
    //         'email' => ['required'],
    //         'promo_code' => ['sometimes'],
    //         'state' => 'required',
    //         'lga' => 'required',
    //         'sender_name' => 'required',
    //         'street' => ['required'],
    //         'address'=> 'required',
    //         'cart_id' => 'required|array',
    //         'price' => 'required|numeric',
    //         'description_id' => 'required|array',
    //     ];

    //     $validation = Validator::make($request->all(), $rules);
    //     if ( $validation->fails() ) {
    //         return ApiResponse::validationError([
    //                 "message" => $validation->errors()->first()
    //         ]);
    //     }

    //     $cart_ids = [];
    //     foreach ($request->cart_id as $cart) {
    //         $cart_ids[] = $cart;
    //     }

    //     $description_ids = [];
    //     foreach ($request->description_id as $description) {
    //         $description_ids[] = $description;
    //     }

    //     $random = Str::random(20);
    //     $randomNumber = random_int(10000, 99999);
    //     $email = '';
    //     if ($request->email){
    //         $email = $request->email;
    //     } else {
    //         $email = 'useremail@gmail.com';
    //     }

    //     $user = User::where('id', Auth::id())->first();

    //     $promo_codes = $user->promo_codes;

    //     $new_codes = [];

    //     $slashed_price = '';

    //     if($request->promo_code !== '') {
    //         if($promo_codes == null) {
    //             $new_codes[] = $request->promo_code;
    
    //             $user->update([
    //                 'promo_codes' => $new_codes
    //             ]);

    //             $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
            
    //             $price = [];
    //             // $id = ''; 
                
    //             foreach ($cart as $cart_item) {
    //                 $price[] = $cart_item->price;
    //             }

    //             $slashed_price = array_sum($price) - $request->price;
    //         } else {
    //             foreach ($promo_codes as $code) {
    //                 $new_codes[] = $code;
    //             }
    //             $new_codes[] = $request->promo_code;
    
    //             $user->update([
    //                 'promo_codes' => $new_codes
    //             ]);

                
    //             $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
            
    //             $price = [];
    //             // $id = ''; 
                
    //             foreach ($cart as $cart_item) {
    //                 $price[] = $cart_item->price;
    //             }

    //             $slashed_price = array_sum($price) - $request->price;
    //         }

    //     }
        

    //     $data = array(
    //         "amount" => $request->price * 100,
    //         "reference" => $random,
    //         "email" => $email,
    //         "currency" => "NGN",
    //         "id" => $randomNumber,
    //         'callback_url' => 'http://localhost:5173/processing'
    //     );
        


    //     if ($slashed_price !== ''){
    //         $checkout = Checkout::create([
    //             'user_id' => Auth::id(),
    //             'state' => $request->state,
    //             'lga' => $request->lga,
    //             'street' => $request->street,
    //             'price' => $request->price,
    //             'address' => $request->address,
    //             'sender_name' => $request->sender_name,
    //             'description_id' => $description_ids,
    //             'cart_id' => $cart_ids,
    //             'pay_ref' => $random,
    //             'slashed_price' => $slashed_price,
    //         ]);
    //     } else {
    //         $checkout = Checkout::create([
    //             'user_id' => Auth::id(),
    //             'state' => $request->state,
    //             'lga' => $request->lga,
    //             'street' => $request->street,
    //             'price' => $request->price,
    //             'address' => $request->address,
    //             'sender_name' => $request->sender_name,
    //             'description_id' => $description_ids,
    //             'cart_id' => $cart_ids,
    //             'pay_ref' => $random,
    //         ]);

    //     }

    // }


    //UNCOMMENT FOR PAYSTACK
    public function store(Request $request)
    {
        $rules = [
            'email' => ['required'],
            'promo_code' => ['sometimes'],
            'state' => 'required',
            'lga' => 'required',
            'street' => ['required'],
            'address'=> 'required',
            'sender_name'=> 'required',
            'cart_id' => 'required|array',
            'price' => 'required|numeric',
            'description_id' => 'required|array',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $cart_ids = [];
        foreach ($request->cart_id as $cart) {
            $cart_ids[] = $cart;
        }

        $description_ids = [];
        foreach ($request->description_id as $description) {
            $description_ids[] = $description;
        }

        $random = Str::random(20);
        $randomNumber = random_int(10000, 99999);
        $email = '';
        if ($request->email){
            $email = $request->email;
        } else {
            $email = 'useremail@gmail.com';
        }

        $user = User::where('id', Auth::id())->first();

        $promo_codes = $user->promo_codes;

        $new_codes = [];

        $slashed_price = '';

        if($request->promo_code !== '') {
            if($promo_codes == null) {
                $new_codes[] = $request->promo_code;
    
                $user->update([
                    'promo_codes' => $new_codes
                ]);

                $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
            
                $price = [];
                // $id = ''; 
                
                foreach ($cart as $cart_item) {
                    $price[] = $cart_item->price;
                }

                $slashed_price = array_sum($price) - $request->price;
            } else {
                foreach ($promo_codes as $code) {
                    $new_codes[] = $code;
                }
                $new_codes[] = $request->promo_code;
    
                $user->update([
                    'promo_codes' => $new_codes
                ]);

                
                $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
            
                $price = [];
                // $id = ''; 
                
                foreach ($cart as $cart_item) {
                    $price[] = $cart_item->price;
                }

                $slashed_price = array_sum($price) - $request->price;
            }

        }
        

        $data = array(
            "amount" => $request->price * 100,
            "reference" => $random,
            "email" => $email,
            "currency" => "NGN",
            "id" => $randomNumber,
            'callback_url' => 'https://fabricspa.com.ng/processing'
            // 'callback_url' => 'http://localhost:5173/processing'
        );
        
        $paystack = new Paystack();

      

        $response = Http::withHeaders([
            "Authorization"=> 'Bearer '.env('PAYSTACK_SECRET_KEY'),
            "Cache-Control" => 'no-cache',
        ])->post('https://api.paystack.co/transaction/initialize', $data);
        $res = json_decode($response->getBody());

            
        if ($slashed_price !== ''){
            $checkout = Checkout::create([
                'user_id' => Auth::id(),
                'state' => $request->state,
                'lga' => $request->lga,
                'street' => $request->street,
                'price' => $request->price,
                'address' => $request->address,
                'sender_name' => $request->sender_name,
                'description_id' => $description_ids,
                'cart_id' => $cart_ids,
                'pay_ref' => $random,
                'slashed_price' => $slashed_price,
            ]);
        } else {
            $checkout = Checkout::create([
                'user_id' => Auth::id(),
                'state' => $request->state,
                'lga' => $request->lga,
                'street' => $request->street,
                'price' => $request->price,
                'address' => $request->address,
                'sender_name' => $request->sender_name,
                'description_id' => $description_ids,
                'cart_id' => $cart_ids,
                'pay_ref' => $random,
            ]);

        }


        return response()->json([
        'paymentLink' => $res->data->authorization_url
        ]);

    }


    public function handleCallback( string $id)
{
    $paystack = new Paystack();
    $client = new Client();
    // return env('PAYSTACK_PAYMENT_URL').'/paymentrequest/'.$id;
    $record = $client->request('GET', env('PAYSTACK_PAYMENT_URL').'/transaction/verify/'.$id,
    ['headers' => ['Authorization' => 'Bearer '.env('PAYSTACK_SECRET_KEY')]]);

    $payment = json_decode($record->getBody())->data;
    // return ;
    $checkout = Checkout::where('pay_ref', $payment->reference)->first();

    if($checkout->is_verified == false) {
        $user = User::where('id', Auth::id())->first();

        $checkout->update([
            'amount_received' => $payment->amount/100
        ]);

        foreach ($checkout->cart_id as $id) {
            $cart = Cart::where('id', $id)->first();

            $cart->update([
                'is_paid' => true
            ]);

            $order = Order::where('id', $cart->order_id)->first();
            $order->update([
                'status' =>'paid',
                'checkout_id' => $checkout->id
            ]);
            $types = Type::create([
                'name' => $cart->feature,
                'user_id' => Auth::id(),
                'order_id' => $cart->order_id,
                'status' => 'paid'
            ]);
        }

        // foreach ($checkout->description_id as $id) {
        //     $description = Description::where('id', $id)->first();

        //     $feature = Feature::where('id', $description->feature_id)->first();
         
        // }
        
        
        $inventory = Inventory::create([
            'date' => Carbon::now(),
            'checkout_id' => $checkout->id,
            'amount' => $payment->amount/100,
        ]);
        
        NotificationController::Notify(Auth::id(), "You've have paid ₦".($payment->amount/100).".00 and cleared your cart, so do well to bring the clothes to office for washing, or contact us for home pickup. If you've forgotten what you ordered, kindly check the history page to see it again.", Carbon::now(), 'success', 'payment');
        
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            
            NotificationController::Notify($admin->id, "A customer has made a payment of ₦".($payment->amount/100).".00, so go to the active orders to view the customer's request and attend to it duely.", Carbon::now(), 'success', 'payment');
            
        }
        $checkout->update([
            'is_verified' => true
        ]);
        
    } else {
        return ApiResponse::errorResponse('This payment has been processed already');
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }
}
