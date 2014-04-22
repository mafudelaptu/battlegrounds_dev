<?php

class MatchtypesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('matchtypes')->truncate();
		switch(App::environment()){
			case "local_ih":
			case "ih":
			$single5vs5 = 0;
			$oneVsOne = 1;
			$team5vs5 = 0;
			break;
			case "local":
			default:
			$single5vs5 = 1;
			$oneVsOne = 1;
			$team5vs5 = 0;
		}

		$matchtypes = array(
				array("name" => "5vs5-Single", "active" => $single5vs5, "created_at" => new DateTime, "updated_at" => new DateTime, "playercount" => 10),
				array("name" => "1vs1", "active" => $oneVsOne, "created_at" => new DateTime, "updated_at" => new DateTime, "playercount" => 2),
				array("name" => "5vs5-Team", "active" => $team5vs5, "created_at" => new DateTime, "updated_at" => new DateTime, "playercount" => 10),
				);

		// Uncomment the below to run the seeder
		DB::table('matchtypes')->truncate();
		DB::table('matchtypes')->insert($matchtypes);
	}

}
