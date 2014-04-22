<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCreatedEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('created_events', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('event_id');
			$table->integer('eventtype_id');
			$table->timestamp('ended_at');
			$table->integer('team_won_id');
			$table->integer('canceled');
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
		Schema::drop('created_events');
	}

}
