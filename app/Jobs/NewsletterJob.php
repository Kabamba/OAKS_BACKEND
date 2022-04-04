<?php

namespace App\Jobs;

use App\Models\subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {   
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subscribers = subscriber::all();

        foreach ($subscribers as $subscriber) {
            Mail::send('mail.password_reset_mail', ['email' => $subscriber->email], function ($message) use($subscriber) {
                $message->to($subscriber->email);
                $message->subject('Un email de test');
            });
        }

      
    }
}
