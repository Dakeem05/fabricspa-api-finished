<?php

namespace App\Console\Commands;

use App\Mail\UnretievedReminderEmail;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class UnretrievedReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unretrieved-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder email to users with completed orders to come and retrieve their clothes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('status', 'paid')->get();
        
        foreach ($orders as $order) {
            $user = User::where('id', $order->user_id)->first();
            $count = order::where('status', 'paid')->where('user_id', $order->user_id)->count();
            $setting = Setting::where('user_id', $order->user_id)->first();
            if($setting->email_notification == true){
                if($setting->notify_clothes_pickup == true){
                // Mail::to($user->email)->send(new orderReminderEmail($count));
                // Mail::to($user->email)->send(new DeliverReminderEmail($count, $user->last_name));
                Mail::to($user->email)->send(new UnretievedReminderEmail($count, $user->last_name));
                }
            }
        }
        $this->info('Unretrieved reminder emails sent successfully.');
    }
}
