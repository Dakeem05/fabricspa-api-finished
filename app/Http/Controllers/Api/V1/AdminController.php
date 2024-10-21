<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\V1\ApiResponse;
use App\Http\Resources\Api\V1\NotificationCollection;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function setFeatures (Request $request)
    {
        $rules = [
            'name' => 'required',
            'price' => 'required|numeric',
            'contents' => 'array|required',
            'contents.*' => 'required|string',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ( $validation->fails() ) {
            return ApiResponse::validationError([
                    "message" => $validation->errors()->first()
            ]);
        }

        $contents = [];
        $validatedData = $request->all();
        foreach ($validatedData['contents'] as $content) {
            $contents[] = $content;
        }

        // $feature =
    }

    public function dashboard()
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->take(5)->get();
        //$notifications = Notification::where('user_id', Auth::id())->latest()->cursorPaginate(2);

        $user = User::count();

        $inventories = Inventory::all();
        $active_orders = Order::where('status', 'delivered')->count();
        $pending_orders = Order::where('status', 'pending')->count();
        $unretrieved_orders = Order::where('status', 'completed')->count();
        $undelivered_orders = Order::where('status', 'paid')->count();

        $money = [];
        foreach ($inventories as $inventory) {
            $money[] = $inventory->amount;
        }

        $currentWeek = Carbon::now()->week;

        // Retrieve posts created in the current week
        $delivered = Order::where(DB::raw('WEEK(created_at)'), $currentWeek)->where('status', 'delivered')->count();
        $unretrieved = Order::where(DB::raw('WEEK(created_at)'), $currentWeek)->where('status', 'completed')->count();


        return response()->json([
            'notifications' => $notifications,
            'user' => $user,
            'amount' => array_sum($money),
            'active_orders' => $active_orders,
            'pending_orders' => $pending_orders,
            'unretrieved_orders' => $unretrieved_orders,
            'undelivered_orders' => $undelivered_orders,
            'delivered' => $delivered,
            'unretrieved' => $unretrieved,
        ]);
        // return new NotificationCollection($notifications);
    }

    public function users($year, $month)
    {
        // $records = User::whereYear('created_at', $year)
        //     ->whereMonth('created_at', $month)
        //     ->select(DB::raw('WEEK(created_at) as week_number'), DB::raw('COUNT(*) as count'))
        //     ->groupBy(DB::raw('WEEK(created_at)'))
        //     ->get();

        // return response()->json($records);
        $firstDay = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $lastDay = Carbon::createFromDate($year, $month, 1)->endOfMonth();
    
        // Initialize an array to store week counts
        $weekCounts = [];
    
        // Initialize a week number
        $weekNumber = 1;
    
        // Iterate through the weeks
        while ($firstDay <= $lastDay) {
            $weekStart = $firstDay->copy()->startOfWeek();
            $weekEnd = $firstDay->copy()->endOfWeek();
    
            // Count records for the current week
            $count = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();
    
            // Store the count for the week along with the week number
            $weekCounts[] = [
                'weekNumber' => $weekNumber,
                // 'weekStart' => $weekStart->format('Y-m-d'),
                // 'weekEnd' => $weekEnd->format('Y-m-d'),
                'count' => $count,
            ];
    
            // Move to the next week and increment the week number
            $firstDay->addWeek();
            $weekNumber++;
        }
    
        return response()->json($weekCounts);
    }

    public function makeAdmin (string $id)
    {
        $user = User::where('id', $id)->first();

        $user->update([
            'role' => 'admin'
        ]);
    }

    public function unmakeAdmin (string $id)
    {
        $user = User::where('id', $id)->first();

        $user->update([
            'role' => 'client'
        ]);
    }

    public function allUsers ()
    {
        $user = User::latest()->paginate(20);

        return $user;
    }
}