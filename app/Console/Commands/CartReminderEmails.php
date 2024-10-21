<?php

namespace App\Console\Commands;

use App\Mail\CartReminderEmail;
use App\Models\Cart;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CartReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cart-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder email to users with items in their cart';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $carts = Cart::where('is_paid', false)->get();
        
        foreach ($carts as $cart) {

            $user = User::where('id', $cart->user_id)->first();
            $count = Cart::where('is_paid', false)->where('user_id', $cart->user_id)->count();
            $setting = Setting::where('user_id', $cart->user_id)->first();
            if($setting->email_notification == true){
                Mail::to($user->email)->send(new CartReminderEmail($count));
            }
        }
        $this->info('Cart reminder emails sent successfully.');

    }
}
