<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Http\Resources\Api\V1\FeaturesCollection;
use App\Models\Feature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = Feature::all();
        return new FeaturesCollection($features);
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
            'filename' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => ['required', 'unique:'.Feature::class],
            'price' => 'required|numeric',
            'contents' => 'array|required',
            'contents.*' => 'required|string',
        ];

        $validation = Validator::make($request->all(), $rules);
        $validatedData = $request->all();
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $image = $request->name.time().'.'.$request->filename->getClientOriginalName();
            $destinationPath = public_path().'uploads/images';
            $request->filename->move($destinationPath, $image);
            $pathh = $destinationPath.$image;
            $fetut = [];

            // foreach ($validatedData['features'] as $feature) {
                
            //     $imagee = $request->name.time().'.'.$feature['filename']->getClientOriginalName();
            //     $destinationPathh = public_path().'uploads/images';
            //     $feature['filename']->move($destinationPathh, $imagee);
            //     $path = $destinationPathh.$imagee;
              
            //     $fetut[] = [
            //         'name' => $feature['name'],
            //         'image' => $imagee,
            //         'path' => $path
            //     ];
                
            // }
        $contents = [];
        foreach ($request->only('contents') as $content) {
            $contents[] = $content;
        }



        $feature = Feature::create([
            'name' => $request->name,
            'filename' => $image,
            'price' =>  $request->price,
            'contents' => $contents
        ]);

    // return NotificationController::Notify(Auth::id(), 'New feature created successfully', Carbon::now(), 'success');
        return response()->json([
            'feature' => $feature,
            'notification' => NotificationController::Notify(Auth::id(), 'New feature created successfully!!', Carbon::now(), 'success', 'creation')
        ]);    
    }

    public function edit(Request $request, string $id)
    {
        $rules = [
            'name' => ['required'],
            'price' => 'required|numeric',
            'contents' => 'array|required',
            'contents.*' => 'required|string',
        ];

        $validation = Validator::make($request->all(), $rules);
        $validatedData = $request->all();
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }
            // foreach ($validatedData['features'] as $feature) {
                
            //     $imagee = $request->name.time().'.'.$feature['filename']->getClientOriginalName();
            //     $destinationPathh = public_path().'uploads/images';
            //     $feature['filename']->move($destinationPathh, $imagee);
            //     $path = $destinationPathh.$imagee;
              
            //     $fetut[] = [
            //         'name' => $feature['name'],
            //         'image' => $imagee,
            //         'path' => $path
            //     ];
                
            // }
        $contents = [];
        foreach ($request->only('contents') as $content) {
            $contents[] = $content;
        }



        Feature::where('id', $id)->update([
            'name' => $request->name,
            'price' =>  $request->price,
            'contents' => $contents
        ]);

    // return NotificationController::Notify(Auth::id(), 'New feature created successfully', Carbon::now(), 'success');
        return response()->json([
            'notification' => NotificationController::Notify(Auth::id(), 'Feature updated successfully!!', Carbon::now(), 'success', 'update')
        ]);    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $name)
    {
        $feature = Feature::where('name', $name)->get();
        return new FeaturesCollection($feature);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $featureInstance = Feature::findOrFail($id);

        $rules = [
            'name' => ['sometimes', 'unique:'.Feature::class],
            'price' => 'sometimes|numeric',
            'contents' => 'array|sometimes',
            'contents.*' => 'required|string',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $contents = [];
        foreach ($request->only('contents') as $content) {
            $contents[] = $content;
        }

        if($request->only('name')){
            $featureInstance->update([
                'name' => $request->name
            ]);
            // return ApiResponse::successResponse('Changes updated successfully');
        } else if ($request->only('price')) {
            $featureInstance->update([
                'price' => $request->price
            ]);
            // return ApiResponse::successResponse('Changes updated successfully');
        } else {
            $content = [];
            $backend_feature = $featureInstance->contents;
            $request_feature = $request->contents;
            $feat = '';
            foreach ($backend_feature as $feature) {
                $content[] = $feature;
            }
            foreach ($request_feature as $feature) {
                $contents = Arr::flatten($content);
                // return $contents;
                if(array_search($feature, $contents) == true){
                    return ApiResponse::errorResponse('Already exixts');
                } else{
                    $content[] = $feature;
                }
            }
            $featureInstance->update([
                'contents' => Arr::flatten($content)
            ]);
            // return ApiResponse::successResponse('Changes updated successfully');
        }
        return $featureInstance;    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Feature::findOrFail($id)->delete();
        return ApiResponse::successResponse('deleted');
    }
}
