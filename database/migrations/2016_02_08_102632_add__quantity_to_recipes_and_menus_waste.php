<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantityToRecipesAndMenusWaste extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wastes', function ($table) {
            $table->integer('recipe_count')->unsigned()->nullable();
            $table->integer('menu_count')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wastes', function($table)
        {
            $table->dropColumn('recipe_count');
            $table->dropColumn('menu_count');
        });
    }

}
