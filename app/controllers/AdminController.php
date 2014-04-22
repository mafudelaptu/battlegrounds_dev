<?php

class AdminController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	protected $layout = "admin.master";
	protected $title = "Admin Panel";

	public function home()
	{
		$this->layout->title = $this->title;
		
		$contentData = array(
			"heading" => $this->title,
			"matchtypes" => Matchtype::getAllActiveMatchtypes()->get(),
		);
		$this->layout->nest("content", 'admin.index', $contentData);

	}

	public function showQueueManagement(){
		$title = "Queue-Management";
		$this->layout->title = $this->title." - ".$title;
		
		$contentData = array(
			"heading" => $title,
			"matchtypes" => Matchtype::getAllActiveMatchtypes()->get(),
		);
		$this->layout->nest("content", 'admin.queues.index', $contentData);
	}

	public function showMatchManagement(){
		$title = "Match-Management";
		$this->layout->title = $this->title." - ".$title;
		
		$contentData = array(
			"heading" => $title,
		);
		$this->layout->nest("content", 'admin.matches.index', $contentData);
	}

	public function showBansManagement(){
		$title = "Bans/Warns-Management";
		$this->layout->title = $this->title." - ".$title;
		
		$contentData = array(
			"heading" => $title,
		);
		$this->layout->nest("content", 'admin.bans.index', $contentData);
	}

	
}
