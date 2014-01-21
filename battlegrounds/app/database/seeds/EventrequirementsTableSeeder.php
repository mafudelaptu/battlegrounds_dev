<?php

class EventrequirementsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		 DB::table('eventrequirements')->truncate();

		$eventrequirements = array(
			array("pointborder"=>0, "skillbracketborder"=>0, "winsborder"=>0, "created_at"=>new DateTime),
		);

		// Uncomment the below to run the seeder
		 DB::table('eventrequirements')->insert($eventrequirements);
	}

}
