<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventteamsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eventteams', function(Blueprint $table) {
			$table->integer('event_id');
			$table->integer('created_event_id');
			$table->bigInteger('user_id');
			$table->integer('round');
			$table->integer('eventteam_id');
			$table->integer('points');
			$table->timestamps();

			$table->primary(array("event_id", "created_event_id", "user_id", "round"),"id_eventteams_primary");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('eventteams');
	}

}
