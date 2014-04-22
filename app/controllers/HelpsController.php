<?php

class HelpsController extends BaseController {

	/**
	 * Help Repository
	 *
	 * @var Help
	 */
	protected $help;

	public function __construct(Help $help)
	{
		$this->help = $help;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$helps = $this->help->all();

		return View::make('helps.index', compact('helps'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('helps.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		$input = General::unsetByKeyStartStr("/arena/", $input);
		
		$validation = Validator::make($input, Help::$rules);

		if ($validation->passes())
		{
			$this->help->create($input);

			return Redirect::route('admin.helps.index');
		}

		return Redirect::route('admin.helps.create')
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
		$help = $this->help->findOrFail($id);

		return View::make('helps.show', compact('help'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$help = $this->help->find($id);
		if (is_null($help))
		{
			return Redirect::route('admin.helps.index');
		}

		return View::make('helps.edit', compact('help'));
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
		foreach($input as $k => $v){
			if(str_contains($k, "/helps/")){
				unset($input[$k]);
			}
		}
		//dd($input);
		$validation = Validator::make($input, Help::$rules);

		if ($validation->passes())
		{
			$help = $this->help->find($id);
			$help->update($input);

			return Redirect::route('admin.helps.show', $id);
		}

		return Redirect::route('admin.helps.edit', $id)
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
		$this->help->find($id)->delete();

		return Redirect::route('admin.helps.index');
	}

}
