<?php

class GlobalsettingsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('globalsettings')->truncate();

		switch(App::environment()){
			case "local_ih":
			case "ih":
			$DefaultRegion = 10;
				$QuickJoinMatchmode = 9;
				$LoginVia = "Steam";
				$ForumLink = "http://dev.battlegrounds/forum/?page=forum";
				$ForumHost = "IHearthU";
				$ReplayUpload = "";
				$ReplayUploadActive = 0;
				$HeroListUpdateDay = 0;
				$HeroListUpdateDayActive = 0;
				$OnevsOneMatchmode = 1;
				break;
			case "local":
			default:
				$DefaultRegion = 1;
				$QuickJoinMatchmode = 9;
				$LoginVia = "Forum_IPBoard";
				$ForumLink = "http://dotacinema.localhost/forum/?page=forum";
				$ForumHost = "Dotacinema";
				$ReplayUpload = "Dota2";
				$ReplayUploadActive = 1;
				$HeroListUpdateDay = 1;
				$HeroListUpdateDayActive = 1;
				$OnevsOneMatchmode = 5;
		}

		$globalsettings = array(
			array("name" => "BasePoints","value" => "1200","active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set the value of starting Points of each player (INTEGER, Default:1200)"),
			array("name" => "DefaultRegion","value" => $DefaultRegion,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set the the default region of the arena system (INTEGER). Possible values: 1=EU, 2=EUE, 3=USW, 4=USE, 5=RU, 6=SEA, 7=AU/NZ, 8=SA, 9=CH"),
			array("name" => "DuoJoin","value" => "","active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set if DUO-Queue Join should be active (setting via 'active' column)"),
			array("name" => "justCM","value" => "","active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set if just one Matchmode (eg: CM) is possible in the entire system (setting via 'active' column)"),
			array("name" => "QueueLockTime","value" => "180","active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"How long should a user get locked from queue after dont accepting a match or being AFK (INTEGER, Default:180)"),
			array("name" => "WeeklyUpvoteCount","value" => "10","active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"how many upvotes should a player have per week (INTEGER, Default:10)"),
			array("name" => "WeeklyDownvoteCount","value" => "5","active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"how many upvotes should a player have per week (INTEGER, Default:5)"),
			array("name" => "CreditBronzeBorder","value" => "25","active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"with which credit-count should a player reach the bronze status (INTEGER, Default:25)"),
			array("name" => "CreditSilverBorder","value" => "125","active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"with which credit-count should a player reach the silver status (INTEGER, Default:125)"),
			array("name" => "CreditGoldBorder","value" => "250","active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"with which credit-count should a player reach the bronze status (INTEGER, Default:250)"),
			array("name" => "MatchLeaverPunishment","value" => "-50","active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"Amount of points a player lose after successfully determined that he left the match(INTEGER, Default:-50)"),
			array("name" => "QuickJoinMatchmode","value" => $QuickJoinMatchmode,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"Which matchmode is the quick-join-matchmode (INTEGER, Default:9). Possible values: 1=AP, 2=SD, 3=CM, 4=ARAM, 5=OM, 6=LM, 7=RD, 8=AR, 9=CD, 10=EM, 11=DM"),
			array("name" => "BanCreditBorder","value" => -15,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"Set the bottom credit border, when a player should get auto-banned (INTEGER, Default:-15)"),
			array("name" => "WeeklyVoteCountUpdateDay","value" => 1,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"When (day) should the Votecounts of each user  get updated (INTEGER, Default:1). Possible values: 1=mo, 2=tu, 3=we, 4=th, 5=fr, 6=sa, 7=su"),
			array("name" => "PermaBanBorder","value" => 6,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"with which amount of active bans a user should get auto-permabanned (INTEGER, Default:6)"),
			array("name" => "BanDecayTime","value" => 1728000,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"After which amount of time should the active bans get decayed (INTEGER, Default:1728000) [20 days]"),
			array("name" => "TeamsActive","value" => "","active" => 0, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"Able to create teams? (setting via 'active' column)"),
			array("name" => "LoginVia","value" => $LoginVia,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set the primary login method (STRING, Default:'Steam'). Possible values: 'Steam', 'Forum_IPBoard'"),
			array("name" => "ForumLink","value" => $ForumLink,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"Link to Forum if LoginVia = 'Forum_IPBoard'  (STRING, Default:'http://forum.link')"),
			array("name" => "ForumHost","value" => $ForumHost,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"Host of the forum if LoginVia = 'Forum_IPBoard' (STRING, Default:'Dotacinema'), Possible values: 'Dotacinema'"),
			array("name" => "EventStartSubmissionBorder","value" => 3600,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set how many seconds before the start of an event users can sign-in (INTEGER, Default:3600)"),
			array("name" => "EventEndSubmissionBorder","value" => 600,"active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set how many seconds before the start of an event users can't sign-in anymore (INTEGER, Default:600)"),
			array("name" => "EventWinBonus","value" => 75, "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"Point-Bonus for winning an event (INTEGER, Default:50)"),
			array("name" => "ForumArenaActive","value" => "", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set if arena should be accessible or not(setting via 'active' column)"),
			array("name" => "ReplayUpload","value" => $ReplayUpload, "active" => $ReplayUploadActive, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set if Replayupload is possible on match-page,(setting via 'active' column [enabled/disabled]) Default:Dota2. Possible values: Dota2"),
			array("name" => "ReplayUploadSourceDir","value" => "server/php/files/", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set source directory where replay files gets uploaded - with '/' at end (STRING, Default:'server/php/files/')."),
			array("name" => "ReplayUploadDestDir","value" => "files/dota2_replays/", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set destination directory where replay files gets moved after upload - with '/' at end (STRING, Default:'files/dota2_replays/')."),
			array("name" => "SteamAPIKey","value" => "C086AB50054408B7A0FA42ABC467EAC9", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set Steam WEB-API Key (STRING, Default:'C086AB50054408B7A0FA42ABC467EAC9')."),
			array("name" => "HeroListUpdateDay","value" => "1", "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set Steam WEB-API Key (INTEGER, Default:'1' [monday]). Possible values: 1=mo, 2=tu, 3=we, 4=th, 5=fr, 6=sa, 7=su"),
			array("name" => "1vs1Matchmode","value" => $OnevsOneMatchmode, "active" => 1, "created_at" => new DateTime, "updated_at" => new DateTime, "description" =>"set which matchmode gets played in 1vs1 (INTEGER, Default:'1' [monday]). Possible values: 1=mo, 2=tu, 3=we, 4=th, 5=fr, 6=sa, 7=su"),
			
		);

		// Uncomment the below to run the seeder
		DB::table('globalsettings')->truncate();
		DB::table('globalsettings')->insert($globalsettings);
	}

}
