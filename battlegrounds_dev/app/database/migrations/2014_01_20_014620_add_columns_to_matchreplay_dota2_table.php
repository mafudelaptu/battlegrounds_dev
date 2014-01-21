<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnsToMatchreplayDota2Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::select(DB::raw('ALTER TABLE matchreplay_dota2s MODIFY COLUMN id BIGINT'));
		Schema::table('matchreplay_dota2s', function(Blueprint $table) {
			
			$table->integer("gamelength");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::select(DB::raw('ALTER TABLE matchreplay_dota2s MODIFY COLUMN id INT'));
		Schema::table('matchreplay_dota2s', function(Blueprint $table) {
		$table->dropColumn("gamelength");
		});
	}

}