<?php

class ChatsController extends BaseController {

	public function postMessage(){
		$ret = array();
		if(Auth::check()){
				
				$user_id = Auth::user()->id;
				$msg = Input::get("msg");
				$section = Input::get("section").Auth::user()->region_id;

				Chat::insertChatMessage($section, $user_id, $msg);
				$ret['status'] = true;
			
		}
		return $ret;
	}	
	public function getOlderMessages(){
		$ret = array();
		if(Auth::check()){
				$date = new DateTime();
				$section = Input::get("section").Auth::user()->region_id; // dc fix for getting match_id
				$lastTimestamp = Input::get("last");
				$lastTimestamp = round($lastTimestamp/1000);

				$lastTimestamp = $date->setTimestamp($lastTimestamp);
// dd($lastTimestamp);
				$msgs = Chat::getOlderMessages($section, $lastTimestamp)
				->join("users", "users.id", "=", "chats.user_id")
				->select("users.*", 
					"chats.message", 
					"chats.created_at as time"
					)
				->orderBy("chats.created_at", "desc")

				->get();

				$html = "";
				if(!empty($msgs) && count($msgs) > 0){
					$array = array();
					foreach($msgs as $k => $v){
						$tmp = array(
							"user_id" => $v->id,
							"username" => $v->name,
							"avatar" => $v->avatar,
							"time" => $v->time,
							"msg" => $v->message,
							);
						$array[] = $tmp;
					}

					// sort
					krsort($array);
					foreach($array as $k => $v){
						$html .= View::make('prototypes/chat/chatMessage')->with("data",$v)->render();		
					}

					$ret['html'] = $html;
				}
			
		}
		return $ret;
	}	


	/**
	 * Chat Repository
	 *
	 * @var Chat
	 */
	protected $chat;

	public function __construct(Chat $chat)
	{
		$this->chat = $chat;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$chats = $this->chat->all();

		return View::make('chats.index', compact('chats'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('chats.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, Chat::$rules);

		if ($validation->passes())
		{
			$this->chat->create($input);

			return Redirect::route('chats.index');
		}

		return Redirect::route('chats.create')
		->withInput()
		->withErrors($validation)
		->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$chat = $this->chat->findOrFail($id);

		return View::make('chats.show', compact('chat'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$chat = $this->chat->find($id);

		if (is_null($chat))
		{
			return Redirect::route('chats.index');
		}

		return View::make('chats.edit', compact('chat'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Chat::$rules);

		if ($validation->passes())
		{
			$chat = $this->chat->find($id);
			$chat->update($input);

			return Redirect::route('chats.show', $id);
		}

		return Redirect::route('chats.edit', $id)
		->withInput()
		->withErrors($validation)
		->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->chat->find($id)->delete();

		return Redirect::route('chats.index');
	}

}
