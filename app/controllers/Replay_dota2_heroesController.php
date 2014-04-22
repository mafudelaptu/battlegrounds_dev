<?php

class Replay_dota2_heroesController extends BaseController {

	/**
	 * Replay_dota2_hero Repository
	 *
	 * @var Replay_dota2_hero
	 */
	protected $replay_dota2_hero;

	public function __construct(Replay_dota2_hero $replay_dota2_hero)
	{
		$this->replay_dota2_hero = $replay_dota2_hero;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$replay_dota2_heroes = $this->replay_dota2_hero->all();

		return View::make('replay_dota2_heroes.index', compact('replay_dota2_heroes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('replay_dota2_heroes.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, Replay_dota2_hero::$rules);

		if ($validation->passes())
		{
			$this->replay_dota2_hero->create($input);

			return Redirect::route('replay_dota2_heroes.index');
		}

		return Redirect::route('replay_dota2_heroes.create')
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
		$replay_dota2_hero = $this->replay_dota2_hero->findOrFail($id);

		return View::make('replay_dota2_heroes.show', compact('replay_dota2_hero'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$replay_dota2_hero = $this->replay_dota2_hero->find($id);

		if (is_null($replay_dota2_hero))
		{
			return Redirect::route('replay_dota2_heroes.index');
		}

		return View::make('replay_dota2_heroes.edit', compact('replay_dota2_hero'));
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
		$validation = Validator::make($input, Replay_dota2_hero::$rules);

		if ($validation->passes())
		{
			$replay_dota2_hero = $this->replay_dota2_hero->find($id);
			$replay_dota2_hero->update($input);

			return Redirect::route('replay_dota2_heroes.show', $id);
		}

		return Redirect::route('replay_dota2_heroes.edit', $id)
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
		$this->replay_dota2_hero->find($id)->delete();

		return Redirect::route('replay_dota2_heroes.index');
	}

}
