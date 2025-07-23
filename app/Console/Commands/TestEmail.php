<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : The email address to send to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify mail configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $to = $this->argument('email') ?? config('mail.from.address');
        
        $this->info("Sending test email to: {$to}");
        
        try {
            Mail::raw('This is a test email from your Laravel application using Resend.', function ($message) use ($to) {
                $message->to($to)
                    ->subject('Test Email - ' . config('app.name'));
            });
            
            $this->info('✅ Test email sent successfully!');
            $this->info('Check your inbox for the test email.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email:');
            $this->error($e->getMessage());
            
            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }
            
            return Command::FAILURE;
        }
    }
}
