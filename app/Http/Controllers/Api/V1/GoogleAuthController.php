<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect ()
    {
        // return Socialite::driver('google')->redirect();
        return response()->json([
            'redirect_url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    

    public function callbackGoogle (Request $request)
    {
        $code = $request->input('code');

        try {
            // $google_user = Socialite::driver('google')->user();
            $google_user = Socialite::driver('google')->stateless()->user();
            $res = Arr::flatten($google_user->user);

            $user = User::where('google_id', $res[0])->first();
// return $google_user;

            if (!$user) {
                $new_user = User::create([
                    'first_name' => $res[2],
                    'last_name' => $res[3],
                    'email' => $res[5],
                    'picture' => $res[4],
                    'google_id' => $res[0],
                ]);
                
                $token = Auth::login($new_user);
                SettingController::create($new_user->id);

                $admins = User::where('role', 'admin')->get();
                
                foreach ($admins as $admin) {
            
                    NotificationController::Notify($admin->id, "New user, $new_user->email just registered", Carbon::now(), 'success', 'register');
                    
                }
                if($new_user->email == 'edidiongsamuel14@gmail.com'){
                    $user = User::where('email', 'edidiongsamuel14@gmail.com');
                    $user->update([
                        'role' => 'admin'
                    ]);
        
                    // $token = Auth::login($user);
                }
                // return ApiResponse::successResponse([
                //     "data" => [
                //         'message'=> 'Signed up successfully',
                //         "user"=> $user,
                //         'token' => $token
                //         ]
                //     ], 201);
                return ApiResponse::successResponse($token, 200);
            } else {
                $token = Auth::login($user);
                return ApiResponse::successResponse($token, 200);
            }
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
