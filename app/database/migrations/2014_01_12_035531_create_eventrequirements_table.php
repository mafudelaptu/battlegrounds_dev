<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventrequirementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eventrequirements', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('pointborder');
			$table->integer('skillbracketborder');
			$table->integer('winsborder');
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
		Schema::drop('eventrequirements');
	}

}
