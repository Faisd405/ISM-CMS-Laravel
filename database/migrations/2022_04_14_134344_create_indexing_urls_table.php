<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndexingUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indexing_urls', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->index();
            $table->string('module')->nullable();
            $table->unsignedBigInteger('urlable_id')->nullable();
            $table->string('urlable_type')->nullable();
            $table->boolean('locked')->default(false)->comment('1 = tidak bisa didelete , 0 = bisa didelete');
            $table->timestamps();
            $table->softDeletesTz('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('indexing_urls');
    }
}
