<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Http\Resources\Api\V1\DescriptionCollection;
use App\Models\Cart;
use App\Models\Description;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $descriptions = Description::all();
        return new DescriptionCollection($descriptions);
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
        $rules = [
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'colours' => ['required', 'array'],
            'colours.*'=> 'required|string',
            'feature_id'=> 'required|integer',
            'description' => 'sometimes',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $colours = [];
        foreach ($request->only('colours') as $colour) {
            $colours[] = $colour;
        }
      
        $description = Description::create([
            'colour' => $colours,
            'amount' =>  $request->quantity,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'feature_id' => $request->feature_id,
        ]);

        $feature = $description->feature;
        // return $feature->price;
        $order = Order::create([
            'user_id' => Auth::id(),
            // 'slashed_p$request->pricerice'=>
        ]);
        $cart = Cart::create([
            'description_id' => $description->id,
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'feature' => $feature->name,
            'quantity' => $request->quantity,
            'price' => $request->price
            // 'slashed_p$request->pricerice'=>
        ]);

        
        return response()->json([
            'description' => $description,
            'notification' => NotificationController::Notify(Auth::id(), "You've successfully added a service to your cart, go to cart and checkout your service. You ordered for '$feature->name Service' and your billing price is ₦$request->price.00.", Carbon::now(), 'success', 'cart'),
            'cart' => $cart,
            'order' => $order,
        ]);    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $description = Description::where('id', $id)->get();
        return new DescriptionCollection($description);
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
        Description::findOrFail($id)->delete();
        return ApiResponse::successResponse('deleted');
    }

    public function destroyCart(string $id)
    {
        $cart = Cart::findOrFail($id);
        
        $user = User::where('id', $cart->user_id)->first();
        $new_total = $user->cart_total - $cart->price;

        $user->update([
            'cart_total' =>$new_total
        ]);
        $cart->delete();
        return ApiResponse::successResponse('deleted');
    }
}
