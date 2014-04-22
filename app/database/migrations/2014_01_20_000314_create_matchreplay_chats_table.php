<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatchreplayChatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matchreplay_chats', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('matchreplay_id');
			$table->integer('match_id');
			$table->string('name');
			$table->string('time');
			$table->text('msg');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('matchreplay_chats');
	}

}
