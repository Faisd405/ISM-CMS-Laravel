<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnHeaderTextToSomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mod_pages', function (Blueprint $table) {
            $table->string('header_text')->nullable()->after('content');
        });

        Schema::table('mod_content_sections', function (Blueprint $table) {
            $table->string('header_text')->nullable()->after('description');
        });

        Schema::table('mod_links', function (Blueprint $table) {
            $table->string('header_text')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mod_pages', function (Blueprint $table) {
            $table->dropColumn('header_text');
        });

        Schema::table('mod_content_sections', function (Blueprint $table) {
            $table->dropColumn('header_text');
        });

        Schema::table('mod_links', function (Blueprint $table) {
            $table->dropColumn('header_text');
        });
    }
}
