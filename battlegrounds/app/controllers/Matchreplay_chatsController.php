<?php

class Matchreplay_chatsController extends BaseController {

	/**
	 * Matchreplay_chat Repository
	 *
	 * @var Matchreplay_chat
	 */
	protected $matchreplay_chat;

	public function __construct(Matchreplay_chat $matchreplay_chat)
	{
		$this->matchreplay_chat = $matchreplay_chat;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$matchreplay_chats = $this->matchreplay_chat->all();

		return View::make('matchreplay_chats.index', compact('matchreplay_chats'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('matchreplay_chats.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Matchreplay_chat::$rules);

		if ($validation->passes())
		{
			$this->matchreplay_chat->create($input);

			return Redirect::route('matchreplay_chats.index');
		}

		return Redirect::route('matchreplay_chats.create')
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
		$matchreplay_chat = $this->matchreplay_chat->findOrFail($id);

		return View::make('matchreplay_chats.show', compact('matchreplay_chat'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$matchreplay_chat = $this->matchreplay_chat->find($id);

		if (is_null($matchreplay_chat))
		{
			return Redirect::route('matchreplay_chats.index');
		}

		return View::make('matchreplay_chats.edit', compact('matchreplay_chat'));
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
		$validation = Validator::make($input, Matchreplay_chat::$rules);

		if ($validation->passes())
		{
			$matchreplay_chat = $this->matchreplay_chat->find($id);
			$matchreplay_chat->update($input);

			return Redirect::route('matchreplay_chats.show', $id);
		}

		return Redirect::route('matchreplay_chats.edit', $id)
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
		$this->matchreplay_chat->find($id)->delete();

		return Redirect::route('matchreplay_chats.index');
	}

}
