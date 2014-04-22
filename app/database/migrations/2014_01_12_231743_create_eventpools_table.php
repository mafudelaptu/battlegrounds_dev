<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventpoolsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eventpools', function(Blueprint $table) {
			$table->integer('event_id');
			$table->integer('created_event_id');
			$table->integer('round');
			$table->bigInteger('user_id');
			$table->integer('points');
			$table->timestamps();

			$table->primary(array("event_id", "created_event_id", "round", "user_id"), "eventpool_primary");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('eventpools');
	}

}
