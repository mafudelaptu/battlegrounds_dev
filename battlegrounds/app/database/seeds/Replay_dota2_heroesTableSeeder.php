<?php

class Replay_dota2_heroesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('replay_dota2_heroes')->truncate();

		$replay_dota2_heroes = array(
			array("id"=>"npc_dota_hero_antimage", "name"=>"Anti-Mage"),
			array("id"=>"npc_dota_hero_axe", "name"=>"Axe"),
			array("id"=>"npc_dota_hero_bane", "name"=>"Bane"),
			array("id"=>"npc_dota_hero_bloodseeker", "name"=>"Bloodseeker"),
			array("id"=>"npc_dota_hero_earthshaker", "name"=>"Earthshaker"),
			array("id"=>"npc_dota_hero_juggernaut", "name"=>"Juggernaut"),
			array("id"=>"npc_dota_hero_mirana", "name"=>"Mirana"),
			array("id"=>"npc_dota_hero_nevermore", "name"=>"Shadow Fiend"),
			array("id"=>"npc_dota_hero_morphling", "name"=>"Morphling"),
			array("id"=>"npc_dota_hero_crystal_maiden", "name"=>"Crystal Maiden"),
			array("id"=>"npc_dota_hero_drow_ranger", "name"=>"Drow Ranger"),
		);

		// Uncomment the below to run the seeder
		DB::table('replay_dota2_heroes')->insert($replay_dota2_heroes);
	}

}
