<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class RemoveLoginFailed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ahdimcms:removeLoginFailed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus data gagal login agar form di sisi user kembali terbuka';

    private $userService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $faileds = $this->userService->getLoginFailedList([], false);
        $timeNow = now()->format('Y-m-d H');

        foreach ($faileds as $key => $value) {
            $hour = config('cms.module.auth.login.backend.lock_time');
            if ($value->user_type == 0)
                $hour = config('cms.module.auth.login.frontend.lock_time');

            $failedTime = $value->failed_time->addHours($hour)->format('Y-m-d H');
            if ($timeNow >= $failedTime) {
                $value->delete();
                
                sleep(rand(1, 5));
            }
        }

        return 'Delete data failed login successfully';
    }
}
