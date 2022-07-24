<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModGalleryFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mod_gallery_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gallery_category_id')->nullable();
            $table->unsignedBigInteger('gallery_album_id');
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->tinyInteger('type')->default(0)->comment('0 = photo, 1 = video');
            $table->tinyInteger('image_type')->nullable()->comment('0 = image upload, 1 = image fileman, 2 = image url');
            $table->tinyInteger('video_type')->nullable()->comment('0 = video upload, 1 = video youtube');
            $table->text('file');
            $table->text('thumbnail')->nullable();
            $table->json('config')->nullable();
            $table->json('custom_fields')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('publish')->default(true)->comment('1 = publish, 0 draft');
            $table->boolean('public')->default(true)->comment('1 = public, 0 = not public');
            $table->tinyInteger('approved')->default(1)->comment('0 = rejected, 1 = approved, 2 = pending');
            $table->boolean('locked')->default(false)->comment('1 = tidak bisa dihapus, 0 = bisa dihapus');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletesTz('deleted_at', 0);

            $table->foreign('gallery_category_id')->references('id')->on('mod_gallery_categories')
                ->onDelete('SET NULL');
            $table->foreign('gallery_album_id')->references('id')->on('mod_gallery_albums')
                ->cascadeOnDelete();
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
        Schema::dropIfExists('mod_gallery_files');
    }
}
