<?php

class PrizetypesController extends BaseController {

	/**
	 * Prizetype Repository
	 *
	 * @var Prizetype
	 */
	protected $prizetype;

	public function __construct(Prizetype $prizetype)
	{
		$this->prizetype = $prizetype;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$prizetypes = $this->prizetype->all();

		return View::make('prizetypes.index', compact('prizetypes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('prizetypes.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, Prizetype::$rules);

		if ($validation->passes())
		{
			$this->prizetype->create($input);

			return Redirect::route('prizetypes.index');
		}

		return Redirect::route('prizetypes.create')
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
		$prizetype = $this->prizetype->findOrFail($id);

		return View::make('prizetypes.show', compact('prizetype'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$prizetype = $this->prizetype->find($id);

		if (is_null($prizetype))
		{
			return Redirect::route('prizetypes.index');
		}

		return View::make('prizetypes.edit', compact('prizetype'));
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
		$validation = Validator::make($input, Prizetype::$rules);

		if ($validation->passes())
		{
			$prizetype = $this->prizetype->find($id);
			$prizetype->update($input);

			return Redirect::route('prizetypes.show', $id);
		}

		return Redirect::route('prizetypes.edit', $id)
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
		$this->prizetype->find($id)->delete();

		return Redirect::route('prizetypes.index');
	}

}
