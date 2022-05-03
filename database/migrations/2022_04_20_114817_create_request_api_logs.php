<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestApiLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('host');
            $table->string('path');
            $table->text('params')->nullable();
            $table->text('headers')->nullable();
            $table->text('body')->nullable();
            $table->timestamp('request_send');
            $table->integer('response_code');
            $table->text('response_headers');
            $table->text('response_body');
            $table->timestamp('response_received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_api_logs');
    }
}
