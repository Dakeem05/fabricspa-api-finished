<?php

namespace App\Console\Commands;

use App\Mail\BirthdayEmail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBirthdayEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-birthday-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday emails to users whose birthday is today.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereRaw("DAY(STR_TO_DATE(dob, '%d/%m/%Y')) = ?", [now()->day])
        ->whereRaw("MONTH(STR_TO_DATE(dob, '%d/%m/%Y')) = ?", [now()->month])
        ->get();

    foreach ($users as $user) {
        // Send birthday email to $user
        // Use Laravel's Mail facade or any email sending library you prefer
        // Example: Mail::to($user->email)->send(new BirthdayEmail($user));
        $setting = Setting::where('user_id', $user->id)->first();
        if($setting->email_notification == true){
            // Mail::to($user->email)->send(new orderReminderEmail($count));
            Mail::to($user->email)->send(new BirthdayEmail($user));
            // Mail::to($user->email)->send(new DeliverReminderEmail($count, $user->last_name));
        }
        
    }

    $this->info('Birthday emails sent successfully.');
    }
}
