<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_from')->nullable();
            $table->json('user_to')->nullable();
            $table->json('attribute')->nullable();
            $table->text('link');
            $table->json('read_by')->nullable();
            $table->timestamps();

            $table->foreign('user_from')->references('id')->on('users')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feature_notifications');
    }
}
