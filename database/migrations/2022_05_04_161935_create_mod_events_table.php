<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mod_events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->index();
            $table->json('name');
            $table->json('description')->nullable();
            $table->json('form_description')->nullable();
            $table->string('register_code')->nullable();
            $table->tinyInteger('type')->default(0);
            $table->text('place')->nullable();
            $table->json('links')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('email')->nullable();
            $table->json('cover')->nullable();
            $table->json('banner')->nullable();
            $table->json('config');
            $table->json('custom_fields')->nullable();
            $table->json('seo')->nullable();
            $table->text('content_template')->nullable();
            $table->integer('position');
            $table->boolean('publish')->default(true)->comment('1 = publish, 0 draft');
            $table->boolean('public')->default(true)->comment('1 = public, 0 = not public');
            $table->boolean('detail')->default(true)->comment('1 = memiliki halaman, 0 = tidak memiliki halaman');
            $table->tinyInteger('approved')->default(1)->comment('0 = rejected, 1 = approved, 2 = pending');
            $table->boolean('locked')->default(false)->comment('1 = tidak bisa dihapus, 0 = bisa dihapus');
            $table->bigInteger('hits')->default(0);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletesTz('deleted_at', 0);

            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('SET NULL');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('SET NULL');
            $table->foreign('deleted_by')->references('id')->on('users')
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
        Schema::dropIfExists('mod_events');
    }
}
