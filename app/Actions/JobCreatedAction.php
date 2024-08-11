<?php

namespace App\Actions;

use App\Mail\JobPosted;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class JobCreatedAction
{
    /**
     * Handle the action to send emails to all users when a job is created.
     *
     * @return void
     */
    public function __invoke(Job $job)
    {
        $this->sendEmails($job);
    }

    /**
     * Send emails to all users when a job is created.
     *
     * @return void
     */
    public function sendEmails(Job $job)
    {
        // Get all users
        $users = User::all();

        // Queue an email to all users
        foreach ($users as $user) {
            Mail::to($user->email)->queue(new JobPosted($job));
        }
    }
}
