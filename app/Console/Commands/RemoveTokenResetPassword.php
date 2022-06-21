<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveTokenResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ahdimcms:removeTokenResetPassword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus token reset password yang sudah kedaluarsa';

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
     * @return int
     */
    public function handle()
    {
        $passwordReset = DB::table('password_resets')->get();
        $timeNow = now()->format('Y-m-d H');

        foreach ($passwordReset as $key => $value) {
            $createdAt = Carbon::parse($value->created_at)->addHours(3)->format('Y-m-d H');
            if ($timeNow >= $createdAt)
                DB::table('password_resets')->where('token', $value->token)->delete();
                
                // sleep(rand(1, 5));
        }

        return 'Delete token expired reset password successfully';
    }
}
