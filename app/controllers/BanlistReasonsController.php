<?php

class BanlistReasonsController extends BaseController {

	/**
	 * BanlistReason Repository
	 *
	 * @var BanlistReason
	 */
	protected $banlistReason;

	public function __construct(BanlistReason $banlistReason)
	{
		$this->banlistReason = $banlistReason;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$banlistreasons = $this->banlistReason->all();

		return View::make('banlistreasons.index', compact('banlistreasons'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('banlistreasons.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, BanlistReason::$rules);

		if ($validation->passes())
		{
			$this->banlistReason->create($input);

			return Redirect::route('admin.banlistreasons.index');
		}

		return Redirect::route('admin.banlistreasons.create')
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
		$banlistReason = $this->banlistReason->findOrFail($id);

		return View::make('banlistreasons.show', compact('banlistReason'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$banlistReason = $this->banlistReason->find($id);

		if (is_null($banlistReason))
		{
			return Redirect::route('admin.banlistreasons.index');
		}

		return View::make('banlistreasons.edit', compact('banlistReason'));
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
		$validation = Validator::make($input, BanlistReason::$rules);

		if ($validation->passes())
		{
			$banlistReason = $this->banlistReason->find($id);
			$banlistReason->update($input);

			return Redirect::route('admin.banlistreasons.show', $id);
		}

		return Redirect::route('admin.banlistreasons.edit', $id)
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
		$this->banlistReason->find($id)->delete();

		return Redirect::route('admin.banlistreasons.index');
	}

}
