<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoginFailedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_faileds', function (Blueprint $table) {
            $table->id();
            $table->ipAddress('ip_address');
            $table->string('username');
            $table->string('password');
            $table->timestamp('failed_time');
            $table->boolean('user_type')->default(0)->comment('0 = backend, 1 = frontend');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_login_faileds');
    }
}
