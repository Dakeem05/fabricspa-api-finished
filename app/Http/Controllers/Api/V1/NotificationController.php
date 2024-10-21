<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\NotificationCollection;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->get();
        //$notifications = Notification::where('user_id', Auth::id())->latest()->cursorPaginate(2);
        return new NotificationCollection($notifications);
    }

    public static function Notify ($id, $message, $time, $type, $category) {
		$notify = Notification::create([
            'user_id' => $id,
            'message' => $message,
            'time' => $time,
            'type' => $type,
            'category' => $category,
        ]);
        return $notify; 
	}
}
