<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
		$this->call('UsersTableSeeder');
		$this->call('GlobalsettingsTableSeeder');
		$this->call('MatchesTableSeeder');
		$this->call('QueuesTableSeeder');
		$this->call('MatchtypesTableSeeder');
		$this->call('MatchmodesTableSeeder');
		$this->call('MatchdetailsTableSeeder');
		$this->call('QueuelocksTableSeeder');
		$this->call('PermabansTableSeeder');
		$this->call('BanlistreasonsTableSeeder');
		$this->call('BanlistsTableSeeder');
		$this->call('VotetypesTableSeeder');
		$this->call('UserpointsTableSeeder');
		$this->call('RegionsTableSeeder');
		$this->call('Matched_usersTableSeeder');
		$this->call('UserskillbracketsTableSeeder');
		$this->call('UsercreditsTableSeeder');
		$this->call('SkillbrackettypesTableSeeder');
		$this->call('MatchhostsTableSeeder');
		$this->call('UservotecountsTableSeeder');
		$this->call('TeamsTableSeeder');
		$this->call('UsernotificationsTableSeeder');
		$this->call('UservotesTableSeeder');
		$this->call('MatchvotesTableSeeder');
		$this->call('PointtypesTableSeeder');
		$this->call('NewsTableSeeder');
		$this->call('StreamersTableSeeder');
		$this->call('EventtypesTableSeeder');
		$this->call('EventrequirementsTableSeeder');
		$this->call('EventsTableSeeder');
		$this->call('EventregistrationsTableSeeder');
		$this->call('Created_eventsTableSeeder');
		$this->call('EventteamsTableSeeder');
		$this->call('EventmatchesTableSeeder');
		$this->call('EventpoolsTableSeeder');
		$this->call('User_won_eventsTableSeeder');
		$this->call('PrizetypesTableSeeder');
		$this->call('TournamenttypesTableSeeder');
		$this->call('Matchreplay_dota2sTableSeeder');
		$this->call('Matchreplay_beststatsTableSeeder');
		$this->call('Matchreplay_chatsTableSeeder');
		$this->call('Replay_beststattypesTableSeeder');
		$this->call('Replay_dota2_heroesTableSeeder');
	}

}