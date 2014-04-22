<?php

class EventrequirementsController extends BaseController {

	/**
	 * Eventrequirement Repository
	 *
	 * @var Eventrequirement
	 */
	protected $eventrequirement;

	public function __construct(Eventrequirement $eventrequirement)
	{
		$this->eventrequirement = $eventrequirement;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$eventrequirements = $this->eventrequirement->all();

		return View::make('eventrequirements.index', compact('eventrequirements'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('eventrequirements.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, Eventrequirement::$rules);

		if ($validation->passes())
		{
			$this->eventrequirement->create($input);

			return Redirect::route('eventrequirements.index');
		}

		return Redirect::route('eventrequirements.create')
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
		$eventrequirement = $this->eventrequirement->findOrFail($id);

		return View::make('eventrequirements.show', compact('eventrequirement'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$eventrequirement = $this->eventrequirement->find($id);

		if (is_null($eventrequirement))
		{
			return Redirect::route('eventrequirements.index');
		}

		return View::make('eventrequirements.edit', compact('eventrequirement'));
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
		$validation = Validator::make($input, Eventrequirement::$rules);

		if ($validation->passes())
		{
			$eventrequirement = $this->eventrequirement->find($id);
			$eventrequirement->update($input);

			return Redirect::route('eventrequirements.show', $id);
		}

		return Redirect::route('eventrequirements.edit', $id)
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
		$this->eventrequirement->find($id)->delete();

		return Redirect::route('eventrequirements.index');
	}

}
