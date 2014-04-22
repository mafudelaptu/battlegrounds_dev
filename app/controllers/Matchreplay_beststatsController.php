<?php

class Matchreplay_beststatsController extends BaseController {

	/**
	 * Matchreplay_beststat Repository
	 *
	 * @var Matchreplay_beststat
	 */
	protected $matchreplay_beststat;

	public function __construct(Matchreplay_beststat $matchreplay_beststat)
	{
		$this->matchreplay_beststat = $matchreplay_beststat;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$matchreplay_beststats = $this->matchreplay_beststat->all();

		return View::make('matchreplay_beststats.index', compact('matchreplay_beststats'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('matchreplay_beststats.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, Matchreplay_beststat::$rules);

		if ($validation->passes())
		{
			$this->matchreplay_beststat->create($input);

			return Redirect::route('matchreplay_beststats.index');
		}

		return Redirect::route('matchreplay_beststats.create')
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
		$matchreplay_beststat = $this->matchreplay_beststat->findOrFail($id);

		return View::make('matchreplay_beststats.show', compact('matchreplay_beststat'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$matchreplay_beststat = $this->matchreplay_beststat->find($id);

		if (is_null($matchreplay_beststat))
		{
			return Redirect::route('matchreplay_beststats.index');
		}

		return View::make('matchreplay_beststats.edit', compact('matchreplay_beststat'));
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
		$validation = Validator::make($input, Matchreplay_beststat::$rules);

		if ($validation->passes())
		{
			$matchreplay_beststat = $this->matchreplay_beststat->find($id);
			$matchreplay_beststat->update($input);

			return Redirect::route('matchreplay_beststats.show', $id);
		}

		return Redirect::route('matchreplay_beststats.edit', $id)
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
		$this->matchreplay_beststat->find($id)->delete();

		return Redirect::route('matchreplay_beststats.index');
	}

}
