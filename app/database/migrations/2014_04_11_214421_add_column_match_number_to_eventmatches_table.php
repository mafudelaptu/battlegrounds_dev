<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnMatchNumberToEventmatchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eventmatches', function(Blueprint $table) {
			$table->integer("match_number");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eventmatches', function(Blueprint $table) {
			$table->dropColumn("match_number");
		});
	}

}
