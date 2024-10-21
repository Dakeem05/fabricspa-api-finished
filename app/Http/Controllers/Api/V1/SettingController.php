<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Http\Resources\Api\V1\SettingCollection;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index(){
    
        $settings = Setting::where('user_id', Auth::id())->get();
        $user = User::where('id', Auth::id())->get();

        $total_settings = [
            'profile' => $user,
            'settings' => $settings
        ];
        return new SettingCollection($total_settings);
    }

    public static function create ($id){
        $settings = Setting::create([
            'user_id' => $id,
            'notify_clothes_pickup' => true,
            'notify_clothes_delivered' => true,
            'notify_clothes_discount_wash' => true,
            'email_notification' => true,
           'sms_notification' => false,
        ]);
        // return $settings;
    }

    public function setNotification (Request $request) {
        $rules = [
            'isEmail' => 'boolean|sometimes',
            'isSms' => 'boolean|sometimes',
            'notify_clothes_pickup' => 'boolean|sometimes',
            'notify_clothes_delivered' => 'boolean|sometimes',
            'notify_clothes_discount_wash' => 'boolean|sometimes',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $settings = Setting::where('user_id', Auth::id())->first();
        // return ApiResponse::errorResponse($request->isEmail);
        if($request->isEmail !== null){
            $settings->update([
               'email_notification' => $request->isEmail 
            ]);
        } else if($request->isSms !== null){
            $settings->update([
               'sms_notification' => $request->isSms
            ]);
        } else if($request->notify_clothes_pickup !== null){
            $settings->update([
               'notify_clothes_pickup' => $request->notify_clothes_pickup
            ]);
        } else if($request->notify_clothes_delivered !== null){
            $settings->update([
               'notify_clothes_delivered' => $request->notify_clothes_delivered
            ]);
        } else if($request->notify_clothes_discount_wash !== null){
            $settings->update([
               'notify_clothes_discount_wash' => $request->notify_clothes_discount_wash
            ]);
        }

        // $user = User::where('id', Auth::id())->get();

        // $total_settings = [
        //     'profile' => $user,
        //     'settings' => $settings
        // ];
        // return new SettingCollection($total_settings);
    }
}
