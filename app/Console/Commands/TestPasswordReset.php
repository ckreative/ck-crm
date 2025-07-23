<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class TestPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset {email? : The email address to send reset link to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test password reset email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            // Get the first user or use the seeded admin
            $user = User::where('email', 'darrell@concretekreative.com')->first() 
                    ?? User::first();
            
            if (!$user) {
                $this->error('No users found in the database.');
                return Command::FAILURE;
            }
            
            $email = $user->email;
        }
        
        $this->info("Testing password reset for: {$email}");
        
        // Send password reset link
        $status = Password::sendResetLink(['email' => $email]);
        
        if ($status === Password::RESET_LINK_SENT) {
            $this->info('✅ Password reset link sent successfully!');
            $this->info('Check the email inbox for the reset link.');
            $this->info('The email was sent via Resend to: ' . $email);
            return Command::SUCCESS;
        } else {
            $this->error('❌ Failed to send password reset link.');
            $this->error('Status: ' . $status);
            return Command::FAILURE;
        }
    }
}