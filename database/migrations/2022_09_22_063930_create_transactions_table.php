<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('portfolio_id')->unsigned();
            $table->foreign('portfolio_id')->references('id')->on('portfolios');
            $table->string('name')->nullable();
            $table->boolean('type')->comment('1. Deposit, 2. Withdraw, 3. Buy, 4. Sell, 5. BO Charge, 6. IPO Charge, 7. Cash Dividend, 8. Stock Dividend');
            $table->integer('organization_id')->unsigned()->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->float('amount')->unsigned()->default(0);
            $table->integer('quantity')->unsigned()->default(1);
            $table->float('commission')->unsigned()->default(0);
            $table->float('tax')->unsigned()->default(0);
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
        Schema::dropIfExists('transactions');
    }
}
