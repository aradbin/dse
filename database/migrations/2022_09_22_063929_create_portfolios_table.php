<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name')->nullable();
            $table->string('bo_account')->nullable();
            $table->integer('broker_id')->unsigned()->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers');
            $table->string('broker_user_id')->nullable();
            $table->float('commission')->default(0.5)->comment('in percentage');
            
            $table->double('deposit')->unsigned()->default(0);
            $table->double('withdraw')->unsigned()->default(0);
            $table->double('balance')->default(0);
            $table->double('buy')->unsigned()->default(0);
            $table->double('sell')->unsigned()->default(0);
            $table->double('paid_commission')->unsigned()->default(0);
            $table->double('paid_charge')->unsigned()->default(0);
            $table->double('paid_tax')->unsigned()->default(0);
            $table->double('realized_gain')->default(0);
            $table->double('cash_dividend')->unsigned()->default(0);
            
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
        Schema::dropIfExists('portfolios');
    }
}
