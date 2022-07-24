<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModEventFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mod_event_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('register_code')->nullable();
            $table->ipAddress('ip_address');
            $table->json('fields')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('exported')->default(false);
            $table->dateTime('submit_time');
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('mod_events')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mod_event_forms');
    }
}
