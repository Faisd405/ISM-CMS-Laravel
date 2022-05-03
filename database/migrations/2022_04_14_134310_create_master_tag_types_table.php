<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterTagTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_tag_types', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->bigInteger('tagable_id')->nullable();
            $table->string('tagable_type')->nullable();
            $table->timestamps();

            $table->foreign('tag_id')->references('id')->on('master_tags')
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
        Schema::dropIfExists('master_tag_types');
    }
}
