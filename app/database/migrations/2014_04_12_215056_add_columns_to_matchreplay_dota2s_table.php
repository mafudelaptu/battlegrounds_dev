<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnsToMatchReplayDota2sTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('matchreplay_dota2s', function(Blueprint $table) {
			$table->integer("gpm");
			$table->integer("xpm");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('matchreplay_dota2s', function(Blueprint $table) {
			$table->dropColumn("gpm");
			$table->dropColumn("pm");
		});
	}

}
