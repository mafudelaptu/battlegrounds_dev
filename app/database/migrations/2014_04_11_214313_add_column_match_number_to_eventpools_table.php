<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnMatchNumberToEventpoolsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eventpools', function(Blueprint $table) {
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
		Schema::table('eventpools', function(Blueprint $table) {
			$table->dropColumn("match_number");
		});
	}

}
