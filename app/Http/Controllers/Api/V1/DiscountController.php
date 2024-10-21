<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Http\Resources\Api\V1\DiscountCollection;
use App\Models\Cart;
use App\Models\Discount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discounts = Discount::all();
        return new DiscountCollection($discounts);
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
            'name' => ['required'],
            'code' => ['required', 'alpha_num', 'unique:'.Discount::class],
            'price_slash' => 'sometimes|numeric',
            'percentage_slash' => 'sometimes|digits_between:1,2',
            'expiration_date' => 'date|sometimes',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }


        $discount = Discount::create([
            'name' => $request->name,
            'price_slash' =>  $request->price_slash,
            'percentage_slash' =>  $request->percentage_slash,
            'expiration_date' =>  $request->expiration_date,
            'code' =>  $request->code,
        ]);

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            
            NotificationController::Notify($admin->id, "An admin has just created a discount code of $request->code ", Carbon::now(), 'success', 'discount');
            
        }

        return $discount;    
    }

    public function applyCode(Request $request)
    {
        $rules = [
            'code' => ['required', 'alpha_num'],
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }
        
        $discount = Discount::where('code', $request->code)->first();

        if($discount == ''){
            return ApiResponse::errorResponse('Invalid discount code');
        } else {
            if($discount->is_expired == true){
                return ApiResponse::errorResponse('Expired discount code');
            } else {
                $id = Auth::id();
                $user = User::where('id', Auth::id())->first();
                
                $actual_code = '';
                if($user->promo_codes == null){
                        if($discount->price_slash == null){

                            $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
            
                            $price = [];
                            // $id = ''; 
                            
                            foreach ($cart as $cart_item) {
                                $price[] = $cart_item->price;
                            }

                            $percentage = array_sum($price) * ($discount->percentage_slash / 100);
                            $new_total = array_sum($price) - $percentage;

                            return ApiResponse::successResponse($new_total);
                        } else {

                            $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
            
                            $price = [];
                            // $id = ''; 
                            
                            foreach ($cart as $cart_item) {
                                $price[] = $cart_item->price;
                            }

                            $new_total = array_sum($price) - $discount->price_slash;

                            return ApiResponse::successResponse($new_total);
                        }
                } else {

                    $collection = collect($user->promo_codes);

                    $filteredCollection = $collection->filter(function ($value) use ($request) {
                        return strpos($value, $request->code) !== false;
                    })->isNotEmpty();
                    if($filteredCollection){
                        return ApiResponse::errorResponse('Already used discount code');
                    } else{

                        
                        // return $discount;
                        if($discount->price_slash == null){
                            $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
    
                            $price = [];
                            // $id = ''; 
                            
                            foreach ($cart as $cart_item) {
                                $price[] = $cart_item->price;
                            }
    
                            $percentage = array_sum($price) * ($discount->percentage_slash / 100);
                            $new_total = array_sum($price) - $percentage;
                            $promo_code = [$request->code];
    
                            return ApiResponse::successResponse($new_total);
                            
                            
                        } else {

                            $cart = Cart::where('user_id', Auth::id())->where('is_paid', false)->get();
    
                            $price = [];
                            // $id = ''; 
                            
                            foreach ($cart as $cart_item) {
                                $price[] = $cart_item->price;
                            }
    
                            $new_total = array_sum($price) - $discount->price_slash;
                            $promo_code = [$request->code];
    
                            return ApiResponse::successResponse($new_total);
                        }

                    }
                }
               
                // return $user;

            }

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
        Discount::findOrFail($id)->delete();
        return ApiResponse::successResponse('deleted');
    }
}
