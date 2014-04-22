<?php

class RegionsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('regions')->truncate();
		switch(App::environment()){
			case "local_ih":
			case "ih":
			$regions = array(
			array("name" => "Europe", "shortcut" => "EU", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Europe-East", "shortcut" => "EUE", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "US-West", "shortcut" => "USW", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "US-East", "shortcut" => "USE", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Russia", "shortcut" => "RU", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Southeast Asia", "shortcut" => "SEA", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Australia/New Zealand", "shortcut" => "AU/NZ", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "South America", "shortcut" => "SA", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "China", "shortcut" => "CH", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "North America", "shortcut" => "NA", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Korea", "shortcut" => "KR", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Latin America", "shortcut" => "LA", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
		);
				break;
			case "local":
			default:
			$regions = array(
			array("name" => "Europe", "shortcut" => "EU", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Europe-East", "shortcut" => "EUE", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "US-West", "shortcut" => "USW", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "US-East", "shortcut" => "USE", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Russia", "shortcut" => "RU", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Southeast Asia", "shortcut" => "SEA", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Australia/New Zealand", "shortcut" => "AU/NZ", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "South America", "shortcut" => "SA", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "China", "shortcut" => "CH", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "North America", "shortcut" => "NA", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Korea", "shortcut" => "KR", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
			array("name" => "Latin America", "shortcut" => "LA", "active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime),
		);
		}
		

		// Uncomment the below to run the seeder
		DB::table('regions')->truncate();
		DB::table('regions')->insert($regions);
	}

}
