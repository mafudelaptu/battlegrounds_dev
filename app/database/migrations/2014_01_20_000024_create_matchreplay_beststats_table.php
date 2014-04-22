<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatchreplayBeststatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matchreplay_beststats', function(Blueprint $table) {
			$table->integer('matchreplay_id');
			$table->integer('match_id');
			$table->bigInteger('user_id');
			$table->integer('replay_beststattype_id');
			$table->integer('value');
			$table->timestamps();

			$table->primary(array("matchreplay_id", "match_id", "user_id", "replay_beststattype_id"), "matchreplay_beststats_primary");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('matchreplay_beststats');
	}

}
