<?php

class MatchmodesController extends BaseController {

	public function getQuickJoinModes(){
		$modes = Matchmode::getQuickJoinModes();
		
		return $modes->get();
	}

	public function getMatchmodeData(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$matchtype_id = Input::get("matchtype_id");
				$selectedArray = Input::get("selectedArray");
				$data = DB::table("matchmodes");
				if(!empty($selectedArray) && count($selectedArray)>0){
					foreach ($selectedArray as $key => $matchmode_id) {
						if($key === 0){
							$data = $data->where("id", $matchmode_id);
						}
						else{
							$data = $data->orWhere("id", $matchmode_id);
						}
					}
					$ret = $data->get();
				}
				else{
					if($matchtype_id == "2"){ // when 1vs1  there is just 1 mm
						$data = $data->where("id",GlobalSetting::get1vs1Matchmode())->first();
						$ret =  array(0=>$data);
					}
					else{
					$ret = array();
					}
				}
			}
		}
		//dd($retData[0]);
		return $ret;
	}

	/**
	 * Matchmode Repository
	 *
	 * @var Matchmode
	 */
	protected $matchmode;

	public function __construct(Matchmode $matchmode)
	{
		$this->matchmode = $matchmode;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$matchmodes = $this->matchmode->all();

		return View::make('matchmodes.index', compact('matchmodes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('matchmodes.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Matchmode::$rules);

		if ($validation->passes())
		{
			$this->matchmode->create($input);

			return Redirect::route('admin.matchmodes.index');
		}

		return Redirect::route('admin.matchmodes.create')
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
		$matchmode = $this->matchmode->findOrFail($id);

		return View::make('matchmodes.show', compact('matchmode'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$matchmode = $this->matchmode->find($id);

		if (is_null($matchmode))
		{
			return Redirect::route('admin.matchmodes.index');
		}

		return View::make('matchmodes.edit', compact('matchmode'));
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
		$validation = Validator::make($input, Matchmode::$rules);

		if ($validation->passes())
		{
			$matchmode = $this->matchmode->find($id);
			$matchmode->update($input);

			return Redirect::route('admin.matchmodes.show', $id);
		}

		return Redirect::route('admin.matchmodes.edit', $id)
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
		$this->matchmode->find($id)->delete();

		return Redirect::route('admin.matchmodes.index');
	}

}
