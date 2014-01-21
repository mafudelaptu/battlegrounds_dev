<?php

class Replay_beststattypesController extends BaseController {

	/**
	 * Replay_beststattype Repository
	 *
	 * @var Replay_beststattype
	 */
	protected $replay_beststattype;

	public function __construct(Replay_beststattype $replay_beststattype)
	{
		$this->replay_beststattype = $replay_beststattype;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$replay_beststattypes = $this->replay_beststattype->all();

		return View::make('replay_beststattypes.index', compact('replay_beststattypes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('replay_beststattypes.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Replay_beststattype::$rules);

		if ($validation->passes())
		{
			$this->replay_beststattype->create($input);

			return Redirect::route('replay_beststattypes.index');
		}

		return Redirect::route('replay_beststattypes.create')
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
		$replay_beststattype = $this->replay_beststattype->findOrFail($id);

		return View::make('replay_beststattypes.show', compact('replay_beststattype'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$replay_beststattype = $this->replay_beststattype->find($id);

		if (is_null($replay_beststattype))
		{
			return Redirect::route('replay_beststattypes.index');
		}

		return View::make('replay_beststattypes.edit', compact('replay_beststattype'));
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
		$validation = Validator::make($input, Replay_beststattype::$rules);

		if ($validation->passes())
		{
			$replay_beststattype = $this->replay_beststattype->find($id);
			$replay_beststattype->update($input);

			return Redirect::route('replay_beststattypes.show', $id);
		}

		return Redirect::route('replay_beststattypes.edit', $id)
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
		$this->replay_beststattype->find($id)->delete();

		return Redirect::route('replay_beststattypes.index');
	}

}
