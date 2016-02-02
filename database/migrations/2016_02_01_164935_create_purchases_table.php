<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('number', 50)->unique();
            $table->date('date_created');
            $table->date('date_delivered');
            $table->dateTime('date_paid')->nullabe();
            $table->boolean('status');
            $table->unsignedInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('files')->onDelete('set null');
            $table->integer('stock_period_id')->unsigned()->nullable();
            $table->foreign('stock_period_id')->references('id')->on('stock_periods')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('purchases');
    }

}
