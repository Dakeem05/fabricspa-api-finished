<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\isNull;

class AuthController extends Controller
{
    public function __construct()
    {   
        $this->middleware('auth:api', ['except' => [
            'login', 
            'store',
            'resendCode',
            'resetPassword',
            'verifyForgot',
            'forgotPassword',
        ]]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'email' => ['required', 'email',  'unique:'.User::class],
            'password' => ['required', 'min:8', "max:30", 'confirmed', Rules\Password::defaults()]
        ];
        $validation = Validator::make( $request->all(), $rules );
        if ( $validation->fails() ) {
            return ApiResponse::validationError([

                    "message" => $validation->errors()->first()
                ]);
        }

        // $referrer = User::where('ref_code', $request->ref_code);
        // $u
        // return $request->phone;
        $randomNumber = random_int(100000, 999999);
        $user = User::create([
            'email'  => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        $token = Auth::login($user);
        if($request->email == 'edidiongsamuel14@gmail.com'){
            $user = User::where('email', 'edidiongsamuel14@gmail.com');
            $user->update([
                'role' => 'admin'
            ]);

            // $token = Auth::login($user);
        }

        SettingController::create($user->id);

        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            
            NotificationController::Notify($admin->id, "New user, $request->email just registered", Carbon::now(), 'success', 'register');
            
        }
        return ApiResponse::successResponse([
            "data" => [
                'message'=> 'Signed up successfully',
                "user"=> $user,
                'token' => $token
                ]
            ], 201);
                
    }

    public function storeDetails(Request $request)
    {

        $rules = [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'username' => ['required', 'min:6', 'max:10',  'unique:'.User::class],
            'phone' => ['required', 'digits:10', 'min:10', 'unique:'.User::class],
            'dob' => ['required', 'before_or_equal:' . now()->subYear()],
        ];
        $validation = Validator::make( $request->all(), $rules );
        if ( $validation->fails() ) {
            return ApiResponse::validationError([

                    "message" => $validation->errors()->first()
                ]);
        }

        $id = Auth::id();

        $user = User::where('id', $id)->first();

        $user->update([
            'first_name' => $request->first_name,
            'username' => $request->username,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'last_name' => $request->last_name,
        ]);
        
        
        return ApiResponse::successResponse('Updated Successfully');
                
    }

    /**
     * Display the specified resource.
     */
    
    public function login (Request $request)
    {
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required']
        ];
        $validation = Validator::make( $request->all(), $rules );
        if ( $validation->fails() ) {
            return ApiResponse::validationError([

                    "message" => $validation->errors()->first()
                ]);
        }
        $credentials = $request->only(['email', 'password']);

        $token = Auth::attempt($credentials);

        if($token) {

            return ApiResponse::successResponse($token, 200);
                    
        } else{
            return ApiResponse::errorResponse("User doesn't exist or wrong details");
        }
    } 

    public function forgotPassword(Request $request)
    {
        $rules = [
            'email' => ['required', 'email',  'exists:'.User::class],
        ];

        $validation = Validator::make($request->all(), $rules);
        
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                "message" => $validation->errors()->first()
            ]);
        } else {
            $user = User::where('email', $request->email)->first();

            if($user->google_id == null) {
                $data = $user->forgot_otp;
                // return $data;
                if($data == '') {
                    $user->sendApiEmailForgotPasswordNotification();
                    return ApiResponse::successResponse('Sent, check your mail');
    
                } else{
                    $data = $user->forgot_otp->delete();
                $user->sendApiEmailForgotPasswordNotification();
                return ApiResponse::successResponse('Sent, check your mail');
            }
        } else{
                return ApiResponse::errorResponse('This user signed up with google account');
            
            }
            // return $user->id;
            
        }
    }
    
    public function resendCode(Request $request)
    {
        $rules = [
            'email' => ['required', 'email',  'exists:'.User::class],
        ];

        $validation = Validator::make($request->all(), $rules);
        
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                "message" => $validation->errors()->first()
            ]);
        } else {
            $user = User::where('email', $request->email)->first();
            $data = $user->forgot_otp->delete();
            $user->sendApiEmailForgotPasswordNotification();
            return ApiResponse::successResponse('Sent, check you mail');
            // return $user->id;
            
        }
    }

    public function verifyForgot (Request $request)
    {
        $rules = [
            'code' => 'digits:6|required',
        ];
        $user = User::where('email' , $request->email)->first();
        // return $request->email;
        $validation = Validator::make( $request->only('code'), $rules );
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                "message" => $validation->errors()->first()
            ]);
        }
        else{
        //     $user  = Auth::user();


            $digit = $user->forgot_otp->otp;
            // return $request->code;
            if($request->code == $digit){
                $user->update(['email_verified_at' => Carbon::now()]);
                $data = $user->forgot_otp->delete();
                return ApiResponse::successResponse(['Updated successfully'], 200);
            } else {
                return  ApiResponse::errorResponse('Wrong code, resend?');
                
            }
        }
    }
    
    public function resetPassword (Request $request)
    {
        $user = User::where('email' , $request->email)->first();
        $rules = [
            'password' => ['required', 'min:8', "max:30", 'confirmed']
        ];

        $validation = Validator::make( $request->all(), $rules );

        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                "message" => $validation->errors()->first()
            ]);
        } else{
            // return $user->email_verified_at;
            if($user->email_verified_at !== null) {
                $user->update([
                    'password' => Hash::make($request->password),
                    'email_verified_at' => null
                ]);
                return ApiResponse::successResponse(['Password changed successfully']);
            } else {
                return  ApiResponse::errorResponse('Have not verified');
            }
        }

    }

    public function logout(Request $request)
    {   
        Auth::logout(true);
        return ApiResponse::successResponse('Logged out');
    }

    public function createToken(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::checkOrFail($token);
            return response()->json(['message' => 'Token valid'], 200);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token expired'], 401);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

    }


    public function getUser()
    {
        $user = Auth::user();

        if ($user) {
            return ApiResponse::successResponse($user);
        } else {
            return ApiResponse::errorResponse('invalid');
        }
    }

    public function deleteUser(Request $request)
    {

        $rules = [
            'password' => ['required'],
        ];
        $validation = Validator::make( $request->all(), $rules );
        if ( $validation->fails() ) {
            return ApiResponse::validationError([

                    "message" => $validation->errors()->first()
                ]);
        }
        $user = User::where('id', Auth::id())->first();

        if (Hash::check($request->password, $user->password)){
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                
                NotificationController::Notify($admin->id, "A user, $user->email, has deleted his/her account", Carbon::now(), 'red', 'deletion');
                
            }
            $user->delete();

        } else{
            return ApiResponse::errorResponse('Wrong password');
             
        }
    }

    public function editProfile (Request $request)
    {
        $rules = [
            'username' => ['sometimes', 'min:6', 'max:10'],
            'first_name' => ['sometimes'],
            'dob' => ['required', 'date_format:d/m/Y', 'before_or_equal:' . now()->subYear()],
            'last_name' => ['sometimes'],
            'email' => ['sometimes', 'email'],
            'phone' => ['sometimes', 'digits:10', 'min:10'],
            'filename' => 'sometimes|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        $validation = Validator::make( $request->all(), $rules );
        if ( $validation->fails() ) {
            return ApiResponse::validationError([

                    "message" => $validation->errors()->first()
                ]);
        }

        $id = Auth::id();
        $user = User::where('id', $id)->first();

        $username = User::where('username', $request->username)->first();
        $email = User::where('email', $request->email)->first();
               
            

        if ($request->filename !== null) {

            $image = $request->username.time().'.'.$request->filename->getClientOriginalName();
            $destinationPath = public_path().'uploads/images';
            $request->filename->move($destinationPath, $image);
            $pathh = $destinationPath.$image;
            if($username){
                if ($user->username !== $request->username){
                    return ApiResponse::errorResponse('Username already exists, use another one.');
    
                }
            } 
            
            if ($email){
                if ($user->email !== $request->email){
                    return ApiResponse::errorResponse('Email already exists, use another one.');
                }
            } 
                $user->update([
                    'first_name' => $request->first_name,
                    'username' => $request->username,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'dob' => $request->dob,
                    'last_name' => $request->last_name,
                    'picture' => 'https://api-control.fabricspa.com.ng/images/'.$image,
                ]);
        } else {

            if($username){
                if ($user->username !== $request->username){
                    return ApiResponse::errorResponse('Username already exists, use another one.');
    
                }
            } 
            
            if ($email){
                if ($user->email !== $request->email){
                    return ApiResponse::errorResponse('Email already exists, use another one.');
                }
            } 
                $user->update([
                    'first_name' => $request->first_name,
                    'username' => $request->username,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'dob' => $request->dob,
                    'last_name' => $request->last_name,
                ]);
        }

        return ApiResponse::successResponse($user);


        // return $request->only('username');
        // if($request->only('username')){
        //     return 'nul';
        // } else {
        //     return  $request->all();

        // }

        // if($request->only('first_name')){
        //     $user->update([
        //         'first_name' => $request->first_name
        //     ]);
        //     // return ApiResponse::successResponse('Changes updated successfully');
        // } else if ($request->only('last_name')) {
        //     $user->update([
        //         'last_name' => $request->last_name
        //     ]);
        //     // return ApiResponse::successResponse('Changes updated successfully');
        // } else if ($request->only('email')){
        //     $email = User::where('email', $request->email)->first();
        //     if($email){
        //         return ApiResponse::errorResponse('Email already exists, use another one.');
        //     } else {
        //         $user->update([
        //             'email' => $request->email
        //         ]);
        //         // return ApiResponse::successResponse('Changes updated successfully');
        //     }
        // } else if ($request->only('username')){
        //     $username = User::where('username', $request->username)->first();
        //     if($username){
        //         return ApiResponse::errorResponse('Username already exists, use another one.');
        //     } else {
        //         $user->update([
        //             'username' => $request->username
        //         ]);
        //         // return ApiResponse::successResponse('Changes updated successfully ');

        //     }
        // } else if ($request->only('dob')) {
        //     $user->update([
        //         'dob' => $request->dob
        //     ]);
        //     // return ApiResponse::successResponse('Changes updated successfully');
        // }  else {
        // // }  else if (!isNull($request->phone)) {
        //     $user->update([
        //         'phone' => $request->phone
        //     ]);  
        //     // return ApiResponse::successResponse('Changes updated successully');
        // }

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
