<?php

class EventtypesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('eventtypes')->truncate();

		$eventtypes = array(
			array(
				"name"=>"test Event friday", 
				"matchmode_id"=>3, 
				"matchtype_id"=>1, 
				"region_id"=>GlobalSetting::getDefaultRegionID(), 
				"tournamenttype_id"=>1, 
				"description"=> "test description test description test description test description test description test description test description test description test description ", 
				"min_submissions"=>20, 
				"start_time"=>"19:00:00", 
				"start_day"=>"friday", 
				"active"=>1, 
				"prizetype_id"=>1,
				"eventrequirement_id"=>1,
				"prizetype_id"=>1,
				"created_at" => new DateTime,
			),
			array(
				"name"=>"test Event sunday", 
				"matchmode_id"=>3, 
				"matchtype_id"=>1, 
				"region_id"=>GlobalSetting::getDefaultRegionID(), 
				"tournamenttype_id"=>1, 
				"description"=> "test description test description test description test description test description test description test description test description test description ", 
				"min_submissions"=>20, 
				"start_time"=>"17:00:00", 
				"start_day"=>"sunday", 
				"active"=>1, 
				"prizetype_id"=>1,
				"eventrequirement_id"=>1,
				"prizetype_id"=>1,
				"created_at" => new DateTime,
			),
		);

		// Uncomment the below to run the seeder
		DB::table('eventtypes')->insert($eventtypes);
	}

}
