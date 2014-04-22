<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReplayDota2HeroesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('replay_dota2_heroes', function(Blueprint $table) {
			$table->string('id');
			$table->string('name');
			$table->timestamps();

			$table->primary("id");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('replay_dota2_heroes');
	}

}
