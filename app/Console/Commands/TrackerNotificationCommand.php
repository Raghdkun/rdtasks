<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TrackerNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infyom:tracker-notification {userName=PMS}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification Send Successfully';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $this->notify('Hello Web Artisan', 'Love beautiful code? We do too!');

        $userName = $this->argument('userName');
        $this->notify(
            'Hello'.' '.$userName,
            'Your tracker time limit is exceed. Please stop your tracker.',
            resource_path('assets/img/logo-red-black.png')
        );
    }
}
