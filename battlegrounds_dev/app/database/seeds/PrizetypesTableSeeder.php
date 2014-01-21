<?php

class PrizetypesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('prizetypes')->truncate();

		$prizetypes = array(
			array("name"=>"+75 Point-Boost", "count"=>1, "type"=>"Point-Boost"),
			array("name"=>"Treasure Key of your choise", "count"=>1, "type"=>"Item (Tool)"),
			array("name"=>"HUD-Skin of you choise", "count"=>1, "type"=>"Item (HUD)"),
			array("name"=>"Item of your choise", "count"=>1, "type"=>"Item (Equipment)"),
			array("name"=>"+100 D2L-Point-Boost", "count"=>1, "type"=>"Point-Boost"),
		);

		// Uncomment the below to run the seeder
		DB::table('prizetypes')->insert($prizetypes);
	}

}
