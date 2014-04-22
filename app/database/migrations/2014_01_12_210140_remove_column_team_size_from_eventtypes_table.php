<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveColumnTeamSizeFromEventtypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eventtypes', function(Blueprint $table) {
			$table->dropColumn("team_size");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eventtypes', function(Blueprint $table) {
			$table->integer("team_size");
		});
	}

}
