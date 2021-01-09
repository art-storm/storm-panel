<?php

namespace App\Console\Commands;

use App\Http\Controllers\Auth\ChangeEmailController;
use Illuminate\Console\Command;

class UsersDeleteEmailChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:deleteEmailChange';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old email change queries';

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
        $instance = new ChangeEmailController();
        $instance->deleteEmailChangeQuery();
    }
}
