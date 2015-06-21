<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function($table) {
            $table->integer('parent_id')->unsigned()->after('level');
            $table->foreign('parent_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function($table)
        {
            $table->dropForeign('roles_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
    }
}
