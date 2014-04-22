<?php

class PermaBansController extends BaseController {


	public function permaBan(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$banned_by = Auth::user()->id;
				$user_id = Input::get("user_id");
				$banreason = Input::get("banreason");
				$bannedBy = Auth::user()->id;

				$ret['status'] = PermaBan::addPermaBan($user_id, $banreason, $bannedBy, 2);

			}
		}
		return $ret;
	}

	public function removePermaBan(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$banned_by = Auth::user()->id;
				$user_id = Input::get("user_id");

				$ret['status'] = PermaBan::removePermaBanOfUser($user_id);

			}
		}
		return $ret;
	}

	/**
	 * PermaBan Repository
	 *
	 * @var PermaBan
	 */
	protected $permaBan;

	public function __construct(PermaBan $permaBan)
	{
		$this->permaBan = $permaBan;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$permaBans = $this->permaBan->all();

		dd("test");
		return View::make('permaBans.index', compact('permaBans'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('permaBans.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




		$validation = Validator::make($input, PermaBan::$rules);

		if ($validation->passes())
		{
			$this->permaBan->create($input);

			return Redirect::route('admin.permaBans.index');
		}

		return Redirect::route('admin.permaBans.create')
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
		$permaBan = $this->permaBan->findOrFail($id);
		return View::make('permaBans.show', compact('permaBan'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$permaBan = $this->permaBan->find($id);

		if (is_null($permaBan))
		{
			return Redirect::route('admin.permaBans.index');
		}

		return View::make('permaBans.edit', compact('permaBan'));
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
		$validation = Validator::make($input, PermaBan::$rules);

		if ($validation->passes())
		{
			$permaBan = $this->permaBan->find($id);
			$permaBan->update($input);

			return Redirect::route('admin.permaBans.show', $id);
		}

		return Redirect::route('admin.permaBans.edit', $id)
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
		$this->permaBan->find($id)->delete();

		return Redirect::route('admin.permaBans.index');
	}

}
