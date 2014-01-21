<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventmatchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eventmatches', function(Blueprint $table) {
			$table->integer('event_id');
			$table->integer('created_event_id');
			$table->integer('match_id');
			$table->integer('round');
			$table->integer('team1');
			$table->integer('team2');
			$table->integer('team_won_id');
			$table->timestamps();

			$table->primary(array("event_id", "created_event_id", "match_id"));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('eventmatches');
	}

}
