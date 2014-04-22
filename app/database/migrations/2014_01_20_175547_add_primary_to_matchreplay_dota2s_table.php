<?php

use Illuminate\Database\Migrations\Migration;

class AddPrimaryToMatchreplayDota2sTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('matchreplay_dota2s', function($table) {
			$table->dropPrimary("id");
			$table->primary(array("id","match_id", "user_id"));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('matchreplay_dota2s', function($table) {
			$table->dropPrimary("match_id");
			$table->dropPrimary("user_id");
		});
	}

}