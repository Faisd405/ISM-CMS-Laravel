<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModInquiryFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mod_inquiry_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inquiry_id');
            $table->ipAddress('ip_address');
            $table->json('fields')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('exported')->default(false);
            $table->dateTime('submit_time');
            $table->timestamps();

            $table->foreign('inquiry_id')->references('id')->on('mod_inquiries')
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
        Schema::dropIfExists('mod_inquiry_forms');
    }
}
