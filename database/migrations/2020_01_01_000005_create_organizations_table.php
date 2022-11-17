<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('category')->nullable();
            $table->string('se_index')->nullable();
            $table->string('instrument_type')->nullable();
            $table->string('sector')->nullable();
            $table->float('market_cap')->nullable();
            $table->float('authorized_cap')->nullable();
            $table->float('paidup_cap')->nullable();
            $table->unsignedBigInteger('shares')->nullable();
            $table->float('director')->nullable();
            $table->float('govt')->nullable();
            $table->float('institute')->nullable();
            $table->float('foreign')->nullable();
            $table->float('public')->nullable();
            $table->float('eps')->nullable();
            $table->float('floor_price')->nullable();
            $table->float('price')->nullable();
            $table->float('pe')->nullable();
            $table->float('upe')->nullable();
            $table->float('nav')->nullable();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();
            $table->softDeletes();

            
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
