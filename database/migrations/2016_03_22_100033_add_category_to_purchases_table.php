<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryToPurchasesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('purchases', function ($table) {
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('purchase_categories')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('purchases', function($table)
		{
			$table->dropForeign('items_category_id_foreign');
			$table->dropColumn('category_id');
		});
	}

}
