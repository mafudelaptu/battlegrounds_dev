<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserWonEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_won_events', function(Blueprint $table) {
			$table->bigInteger('user_id');
			$table->integer('event_id');
			$table->integer('created_event_id');
			$table->timestamps();

			$table->primary(array("user_id", "event_id", "created_event_id"));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_won_events');
	}

}
