<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->after('email')->unique();
            $table->boolean('email_verified')->after('username')->default(false);
            $table->boolean('active')->after('email_verified_at')->default(false);
            $table->timestamp('active_at')->after('active')->nullable();
            $table->string('phone')->after('active_at')->nullable();
            $table->boolean('phone_verified')->after('phone')->default(false);
            $table->json('photo')->after('phone_verified')->nullable();
            $table->bigInteger('userable_id')->after('photo') ->nullable()
                ->comment('id relasi');
            $table->string('userable_type')->after('userable_id')->nullable()
                ->comment('model relasi');
            $table->boolean('locked')->after('userable_type')->default(false)
                ->comment('1 = tidak bisa didelete , 0 = bisa didelete');
                
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
