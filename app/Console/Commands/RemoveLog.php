<?php

namespace App\Console\Commands;

use App\Repositories\UserRepository;
use Illuminate\Console\Command;

class RemoveLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ahdimcms:removeLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus data log user yang sudah melebihi waktu 1 bulan';

    private $userService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userService)
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
        $logs = $this->userService->getLogList([], false);
        $dateNow = now()->format('Y-m-d H');

        foreach ($logs as $log) {
            $logDate = $log->created_at->addMonths(1)->format('Y-m-d');
            if ($logDate == $dateNow)
                $log->delete();

                //sleep(rand(1, 5));
        }

        return 'Delete log user successfully';
    }
}
