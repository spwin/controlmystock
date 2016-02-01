<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRowToStockCheck extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('stock_checks', function ($table) {
			$table->integer('stock_item_id')->unsigned()->nullable();
			$table->foreign('stock_item_id')->references('id')->on('stock_items')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('stock_checks', function($table)
		{
			$table->dropForeign('stock_checks_stock_item_id_foreign');
			$table->dropColumn('stock_item_id');
		});
	}

}
