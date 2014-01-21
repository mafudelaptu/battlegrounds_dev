<?php

use Mockery as m;
use Way\Tests\Factory;

class MatchreplayBeststatsTest extends TestCase {

	public function __construct()
	{
		$this->mock = m::mock('Eloquent', 'Matchreplay_beststat');
		$this->collection = m::mock('Illuminate\Database\Eloquent\Collection')->shouldDeferMissing();
	}

	public function setUp()
	{
		parent::setUp();

		$this->attributes = Factory::matchreplay_beststat(['id' => 1]);
		$this->app->instance('Matchreplay_beststat', $this->mock);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testIndex()
	{
		$this->mock->shouldReceive('all')->once()->andReturn($this->collection);
		$this->call('GET', 'matchreplay_beststats');

		$this->assertViewHas('matchreplay_beststats');
	}

	public function testCreate()
	{
		$this->call('GET', 'matchreplay_beststats/create');

		$this->assertResponseOk();
	}

	public function testStore()
	{
		$this->mock->shouldReceive('create')->once();
		$this->validate(true);
		$this->call('POST', 'matchreplay_beststats');

		$this->assertRedirectedToRoute('matchreplay_beststats.index');
	}

	public function testStoreFails()
	{
		$this->mock->shouldReceive('create')->once();
		$this->validate(false);
		$this->call('POST', 'matchreplay_beststats');

		$this->assertRedirectedToRoute('matchreplay_beststats.create');
		$this->assertSessionHasErrors();
		$this->assertSessionHas('message');
	}

	public function testShow()
	{
		$this->mock->shouldReceive('findOrFail')
				   ->with(1)
				   ->once()
				   ->andReturn($this->attributes);

		$this->call('GET', 'matchreplay_beststats/1');

		$this->assertViewHas('matchreplay_beststat');
	}

	public function testEdit()
	{
		$this->collection->id = 1;
		$this->mock->shouldReceive('find')
				   ->with(1)
				   ->once()
				   ->andReturn($this->collection);

		$this->call('GET', 'matchreplay_beststats/1/edit');

		$this->assertViewHas('matchreplay_beststat');
	}

	public function testUpdate()
	{
		$this->mock->shouldReceive('find')
				   ->with(1)
				   ->andReturn(m::mock(['update' => true]));

		$this->validate(true);
		$this->call('PATCH', 'matchreplay_beststats/1');

		$this->assertRedirectedTo('matchreplay_beststats/1');
	}

	public function testUpdateFails()
	{
		$this->mock->shouldReceive('find')->with(1)->andReturn(m::mock(['update' => true]));
		$this->validate(false);
		$this->call('PATCH', 'matchreplay_beststats/1');

		$this->assertRedirectedTo('matchreplay_beststats/1/edit');
		$this->assertSessionHasErrors();
		$this->assertSessionHas('message');
	}

	public function testDestroy()
	{
		$this->mock->shouldReceive('find')->with(1)->andReturn(m::mock(['delete' => true]));

		$this->call('DELETE', 'matchreplay_beststats/1');
	}

	protected function validate($bool)
	{
		Validator::shouldReceive('make')
				->once()
				->andReturn(m::mock(['passes' => $bool]));
	}
}
