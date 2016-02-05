<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWastesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wastes', function(Blueprint $table)
		{
			$table->increments('id');
            $table->enum('type', ['item', 'recipe', 'menu']);
            $table->integer('reason_id')->unsigned()->nullable();
            $table->foreign('reason_id')->references('id')->on('waste_reasons')->onDelete('set null');
            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->float('value', null, null)->nullable();
            $table->integer('recipe_id')->unsigned()->nullable();
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->integer('menu_id')->unsigned()->nullable();
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->integer('stock_period_id')->unsigned()->nullable();
            $table->foreign('stock_period_id')->references('id')->on('stock_periods')->onDelete('set null');
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
		Schema::drop('wastes');
	}

}
