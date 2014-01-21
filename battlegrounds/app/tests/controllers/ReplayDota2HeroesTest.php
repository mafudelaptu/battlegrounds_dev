<?php

use Mockery as m;
use Way\Tests\Factory;

class ReplayDota2HeroesTest extends TestCase {

	public function __construct()
	{
		$this->mock = m::mock('Eloquent', 'Replay_dota2_hero');
		$this->collection = m::mock('Illuminate\Database\Eloquent\Collection')->shouldDeferMissing();
	}

	public function setUp()
	{
		parent::setUp();

		$this->attributes = Factory::replay_dota2_hero(['id' => 1]);
		$this->app->instance('Replay_dota2_hero', $this->mock);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testIndex()
	{
		$this->mock->shouldReceive('all')->once()->andReturn($this->collection);
		$this->call('GET', 'replay_dota2_heroes');

		$this->assertViewHas('replay_dota2_heroes');
	}

	public function testCreate()
	{
		$this->call('GET', 'replay_dota2_heroes/create');

		$this->assertResponseOk();
	}

	public function testStore()
	{
		$this->mock->shouldReceive('create')->once();
		$this->validate(true);
		$this->call('POST', 'replay_dota2_heroes');

		$this->assertRedirectedToRoute('replay_dota2_heroes.index');
	}

	public function testStoreFails()
	{
		$this->mock->shouldReceive('create')->once();
		$this->validate(false);
		$this->call('POST', 'replay_dota2_heroes');

		$this->assertRedirectedToRoute('replay_dota2_heroes.create');
		$this->assertSessionHasErrors();
		$this->assertSessionHas('message');
	}

	public function testShow()
	{
		$this->mock->shouldReceive('findOrFail')
				   ->with(1)
				   ->once()
				   ->andReturn($this->attributes);

		$this->call('GET', 'replay_dota2_heroes/1');

		$this->assertViewHas('replay_dota2_hero');
	}

	public function testEdit()
	{
		$this->collection->id = 1;
		$this->mock->shouldReceive('find')
				   ->with(1)
				   ->once()
				   ->andReturn($this->collection);

		$this->call('GET', 'replay_dota2_heroes/1/edit');

		$this->assertViewHas('replay_dota2_hero');
	}

	public function testUpdate()
	{
		$this->mock->shouldReceive('find')
				   ->with(1)
				   ->andReturn(m::mock(['update' => true]));

		$this->validate(true);
		$this->call('PATCH', 'replay_dota2_heroes/1');

		$this->assertRedirectedTo('replay_dota2_heroes/1');
	}

	public function testUpdateFails()
	{
		$this->mock->shouldReceive('find')->with(1)->andReturn(m::mock(['update' => true]));
		$this->validate(false);
		$this->call('PATCH', 'replay_dota2_heroes/1');

		$this->assertRedirectedTo('replay_dota2_heroes/1/edit');
		$this->assertSessionHasErrors();
		$this->assertSessionHas('message');
	}

	public function testDestroy()
	{
		$this->mock->shouldReceive('find')->with(1)->andReturn(m::mock(['delete' => true]));

		$this->call('DELETE', 'replay_dota2_heroes/1');
	}

	protected function validate($bool)
	{
		Validator::shouldReceive('make')
				->once()
				->andReturn(m::mock(['passes' => $bool]));
	}
}
