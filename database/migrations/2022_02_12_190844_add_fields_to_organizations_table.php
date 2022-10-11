<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->string('code')->nullable()->after('name');
            $table->string('se_index')->nullable()->after('code');
            $table->string('category')->nullable()->after('se_index');
            $table->string('sector')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            //
        });
    }
}
