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
            $table->double('market_cap')->nullable()->unsigned()->default(0);
            $table->double('authorized_cap')->nullable()->unsigned()->default(0);
            $table->double('paidup_cap')->nullable()->unsigned()->default(0);
            $table->bigInteger('shares')->nullable()->unsigned()->default(0);
            $table->float('director')->nullable()->unsigned()->default(0);
            $table->float('govt')->nullable()->unsigned()->default(0);
            $table->float('institute')->nullable()->unsigned()->default(0);
            $table->float('foreign')->nullable()->unsigned()->default(0);
            $table->float('public')->nullable()->unsigned()->default(0);
            $table->float('eps')->nullable()->default(0);
            $table->float('floor_price')->nullable()->default(0);
            $table->float('price')->nullable()->default(0);
            $table->float('pe')->nullable()->default(0);
            $table->float('upe')->nullable()->default(0);
            $table->float('nav')->nullable()->default(0);

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
