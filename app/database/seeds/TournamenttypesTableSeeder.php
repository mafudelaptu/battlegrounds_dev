<?php

class TournamenttypesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		 DB::table('tournamenttypes')->truncate();

		$tournamenttypes = array(
			array("name"=>"Single-Elimination", "shortcut"=>"SE", "active"=>1, "created_at"=>new DateTime),
		);

		// Uncomment the below to run the seeder
		 DB::table('tournamenttypes')->insert($tournamenttypes);
	}

}
