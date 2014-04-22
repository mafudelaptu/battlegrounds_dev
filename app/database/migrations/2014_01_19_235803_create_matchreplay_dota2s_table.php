<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatchreplayDota2sTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matchreplay_dota2s', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('match_id');
			$table->bigInteger('user_id');
			$table->string('hero');
			$table->integer('kills');
			$table->integer('deaths');
			$table->integer('assists');
			$table->integer('lvl');
			$table->integer('cs');
			$table->integer('denies');
			$table->integer('total_gold');
			$table->integer('first_blood_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('matchreplay_dota2s');
	}

}
