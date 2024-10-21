<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Http\Resources\Api\V1\JobCollection;
use App\Mail\JobEmail;
use App\Models\Job;
use App\Models\JobSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::latest()->get();
        return new JobCollection($jobs);
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
            'title' => ['required'],
            'description' => ['required'],
            'qualification' => ['required'],
            'offer' => ['required'],
            'is_open' => ['required', 'boolean'],
            'type' => ['required'],
            'location' => 'array|sometimes',
            'pay' => 'required',
            'location.*' => 'required|string',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $location = [];
        foreach ($request->only('location') as $content) {
            $location[] = $content;
        }

        $job = Job::create([
            'title' => $request->title,
            'description' => $request->description,
            'qualification' => $request->qualification,
            'offer' => $request->offer,
            'is_open' => $request->is_open,
            'type' => $request->type,
            'pay' => $request->pay,
            'location' => $location
        ]);

        $users = User::all();
        
        // foreach ($users as $user) {
        //     # code...
        //     // return response()->json([
        //     //     '$job' => $job,
        //     //     'notification' => NotificationController::Notify($user->id, 'New Job opening has been posted!!', Carbon::now(), 'success', 'creation')
        //     // ]);
        //     NotificationController::Notify($user->id, 'New Job opening has been posted!!', Carbon::now(), 'success', 'creation');
        // }
        $subscribers = JobSubscription::all();
        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(new JobEmail($request->title, $request->pay));
        }
        foreach ($users as $user) {
            # code...
            return response()->json([
                '$job' => $job,
                'notification' => NotificationController::Notify($user->id, 'New Job opening has been posted!!', Carbon::now(), 'success', 'creation')
            ]);
            // NotificationController::Notify($user->id, 'New Job opening has been posted!!', Carbon::now(), 'success', 'creation');
        }
    }
    public function subscribe(Request $request)
    {
        $rules = [
            'email' => ['required', 'unique:'.JobSubscription::class],
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $job = JobSubscription::create([
            'email' => $request->email,
        ]);

        return ApiResponse::successResponse('Subscribed Successfully!!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = Job::where('id', $id)->first();
        // return $job;
        return response()->json([
            'data' => $job
        ]);
        return new JobCollection($job);
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
        $rules = [
            'title' => ['required'],
            'description' => ['required'],
            'qualification' => ['required'],
            'offer' => ['required'],
            'is_open' => ['required', 'boolean'],
            'type' => ['required'],
            'location' => 'array|sometimes',
            'pay' => 'required',
            'location.*' => 'required|string',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $location = [];
        foreach ($request->only('location') as $content) {
            $location[] = $content;
        }

        $job = Job::where('id', $id)->first();

       $job->update([
            'title' => $request->title,
            'description' => $request->description,
            'qualification' => $request->qualification,
            'offer' => $request->offer,
            'is_open' => $request->is_open,
            'type' => $request->type,
            'pay' => $request->pay,
            'location' => $location
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        Job::findOrFail($id)->delete();
        return ApiResponse::successResponse('deleted');

    }
}
