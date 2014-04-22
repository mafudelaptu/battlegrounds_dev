<?php

class LoginController extends BaseController {

	protected $layout = "master";
	protected $title = "Login Page";

	public function handleLoginPage(){
		$title = $this->title;

		(isset($_SERVER['ENVIRONMENT'])) ? $_SERVER['ENVIRONMENT']=$_SERVER['ENVIRONMENT'] : $_SERVER['ENVIRONMENT'] = "";
		$loginVia = GlobalSetting::getLoginVia();
		switch ($loginVia) {
			case "Forum_IPBoard":
				$redirect = Login::handleForumIPBoardLogin();
				return Redirect::to($redirect);
			break;
			case "Steam":
			default:
			return View::make("start.index")->with("title", $title);
			break;
		}
	}

	public function showForumIPBoard(){
		$title = "You have to login in our Forum";
		return View::make("start.forum_ipboard")->with("title", $title);
	}
}