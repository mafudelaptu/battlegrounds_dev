<?php

class GlobalSettingsController extends BaseController {

	/**
	 * GlobalSetting Repository
	 *
	 * @var GlobalSetting
	 */
	protected $globalsetting;

	public function __construct(GlobalSetting $globalsetting)
	{
		$this->globalsetting = $globalsetting;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$globalsettings = $this->globalsetting->all();

		return View::make('globalsettings.index', compact('globalsettings'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('globalsettings.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, GlobalSetting::$rules);

		if ($validation->passes())
		{
			$this->globalsetting->create($input);

			return Redirect::route('admin.globalsettings.index');
		}

		return Redirect::route('admin.globalsettings.create')
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
		$globalsetting = $this->globalsetting->findOrFail($id);

		return View::make('globalsettings.show', compact('globalsetting'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$globalsetting = $this->globalsetting->find($id);

		if (is_null($globalsetting))
		{
			return Redirect::route('admin.globalsettings.index');
		}

		return View::make('globalsettings.edit', compact('globalsetting'));
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
		$validation = Validator::make($input, GlobalSetting::$rules);

		if ($validation->passes())
		{
			$globalsetting = $this->globalsetting->find($id);
			$globalsetting->update($input);

			return Redirect::route('admin.globalsettings.show', $id);
		}

		return Redirect::route('admin.globalsettings.edit', $id)
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
		$this->globalsetting->find($id)->delete();

		return Redirect::route('admin.globalsettings.index');
	}

}
