<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_configurations', function (Blueprint $table) {
            $table->integer('group');
            $table->string('name')->primary();
            $table->string('label');
            $table->text('value')->nullable();
            $table->boolean('is_upload')->default(false);
            $table->boolean('show_form')->default(true);
            $table->boolean('active')->default(true);
            $table->boolean('locked')->default(false)->comment('1 = tidak bisa dihapus, 0 = bisa dihapus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feature_configurations');
    }
}
