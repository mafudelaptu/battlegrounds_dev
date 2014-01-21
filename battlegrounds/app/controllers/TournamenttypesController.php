<?php

class TournamenttypesController extends BaseController {

	/**
	 * Tournamenttype Repository
	 *
	 * @var Tournamenttype
	 */
	protected $tournamenttype;

	public function __construct(Tournamenttype $tournamenttype)
	{
		$this->tournamenttype = $tournamenttype;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$tournamenttypes = $this->tournamenttype->all();

		return View::make('tournamenttypes.index', compact('tournamenttypes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('tournamenttypes.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Tournamenttype::$rules);

		if ($validation->passes())
		{
			$this->tournamenttype->create($input);

			return Redirect::route('tournamenttypes.index');
		}

		return Redirect::route('tournamenttypes.create')
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
		$tournamenttype = $this->tournamenttype->findOrFail($id);

		return View::make('tournamenttypes.show', compact('tournamenttype'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$tournamenttype = $this->tournamenttype->find($id);

		if (is_null($tournamenttype))
		{
			return Redirect::route('tournamenttypes.index');
		}

		return View::make('tournamenttypes.edit', compact('tournamenttype'));
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
		$validation = Validator::make($input, Tournamenttype::$rules);

		if ($validation->passes())
		{
			$tournamenttype = $this->tournamenttype->find($id);
			$tournamenttype->update($input);

			return Redirect::route('tournamenttypes.show', $id);
		}

		return Redirect::route('tournamenttypes.edit', $id)
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
		$this->tournamenttype->find($id)->delete();

		return Redirect::route('tournamenttypes.index');
	}

}
