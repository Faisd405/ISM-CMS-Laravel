<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->integer('parent')->after('id')->default(0);
            $table->boolean('locked')->default(false)->comment('1 = tidak bisa didelete , 0 = bisa didelete');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->integer('level')->after('name');
            $table->boolean('locked')->default(false)->comment('1 = tidak bisa didelete , 0 = bisa didelete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });

        Schema::table('roles', function (Blueprint $table) {
            //
        });
    }
}
