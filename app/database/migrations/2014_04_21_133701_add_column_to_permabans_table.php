<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
class AddColumnToPermabansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('permaBans', function(Blueprint $table) {
			$table->text("reason");
			$table->bigInteger("bannedBy");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('permaBans', function(Blueprint $table) {
			$table->dropColumn("reason");
			$table->dropColumn("bannedBy");
		});
	}

}