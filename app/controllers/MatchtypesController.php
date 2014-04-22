<?php

class MatchtypesController extends BaseController {

	/**
	 * Matchtype Repository
	 *
	 * @var Matchtype
	 */
	protected $matchtype;

	public function __construct(Matchtype $matchtype)
	{
		$this->matchtype = $matchtype;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$matchtypes = $this->matchtype->all();

		return View::make('matchtypes.index', compact('matchtypes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('matchtypes.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, Matchtype::$rules);

		if ($validation->passes())
		{
			$this->matchtype->create($input);

			return Redirect::route('admin.matchtypes.index');
		}

		return Redirect::route('admin.matchtypes.create')
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
		$matchtype = $this->matchtype->findOrFail($id);

		return View::make('matchtypes.show', compact('matchtype'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$matchtype = $this->matchtype->find($id);

		if (is_null($matchtype))
		{
			return Redirect::route('admin.matchtypes.index');
		}

		return View::make('matchtypes.edit', compact('matchtype'));
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
		$validation = Validator::make($input, Matchtype::$rules);

		if ($validation->passes())
		{
			$matchtype = $this->matchtype->find($id);
			$matchtype->update($input);

			return Redirect::route('admin.matchtypes.show', $id);
		}

		return Redirect::route('admin.matchtypes.edit', $id)
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
		$this->matchtype->find($id)->delete();

		return Redirect::route('admin.matchtypes.index');
	}

}
