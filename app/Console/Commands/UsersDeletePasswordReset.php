<?php

namespace App\Console\Commands;

use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Console\Command;

class UsersDeletePasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:deletePasswordReset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old password reset queries';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $instance = new ResetPasswordController();
        $instance->deletePasswordResetQuery();
    }
}
