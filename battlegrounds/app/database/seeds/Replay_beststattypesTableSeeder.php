<?php

class Replay_beststattypesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		 DB::table('replay_beststattypes')->truncate();

		$replay_beststattypes = array(
			array("name"=>"Most Kills", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"Most Supports", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"Most Last Hits (CS)", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"Most Denies", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"Most Total Gold", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"First Blood", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"Double Kills", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"Triple Kills", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"Quadra Kills", "active"=>1, "created_at"=>new DateTime),
			array("name"=>"Rampage", "active"=>1, "created_at"=>new DateTime),
		);

		// Uncomment the below to run the seeder
		 DB::table('replay_beststattypes')->insert($replay_beststattypes);
	}

}
