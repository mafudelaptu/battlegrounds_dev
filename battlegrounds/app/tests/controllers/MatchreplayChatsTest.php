<?php

use Mockery as m;
use Way\Tests\Factory;

class MatchreplayChatsTest extends TestCase {

	public function __construct()
	{
		$this->mock = m::mock('Eloquent', 'Matchreplay_chat');
		$this->collection = m::mock('Illuminate\Database\Eloquent\Collection')->shouldDeferMissing();
	}

	public function setUp()
	{
		parent::setUp();

		$this->attributes = Factory::matchreplay_chat(['id' => 1]);
		$this->app->instance('Matchreplay_chat', $this->mock);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testIndex()
	{
		$this->mock->shouldReceive('all')->once()->andReturn($this->collection);
		$this->call('GET', 'matchreplay_chats');

		$this->assertViewHas('matchreplay_chats');
	}

	public function testCreate()
	{
		$this->call('GET', 'matchreplay_chats/create');

		$this->assertResponseOk();
	}

	public function testStore()
	{
		$this->mock->shouldReceive('create')->once();
		$this->validate(true);
		$this->call('POST', 'matchreplay_chats');

		$this->assertRedirectedToRoute('matchreplay_chats.index');
	}

	public function testStoreFails()
	{
		$this->mock->shouldReceive('create')->once();
		$this->validate(false);
		$this->call('POST', 'matchreplay_chats');

		$this->assertRedirectedToRoute('matchreplay_chats.create');
		$this->assertSessionHasErrors();
		$this->assertSessionHas('message');
	}

	public function testShow()
	{
		$this->mock->shouldReceive('findOrFail')
				   ->with(1)
				   ->once()
				   ->andReturn($this->attributes);

		$this->call('GET', 'matchreplay_chats/1');

		$this->assertViewHas('matchreplay_chat');
	}

	public function testEdit()
	{
		$this->collection->id = 1;
		$this->mock->shouldReceive('find')
				   ->with(1)
				   ->once()
				   ->andReturn($this->collection);

		$this->call('GET', 'matchreplay_chats/1/edit');

		$this->assertViewHas('matchreplay_chat');
	}

	public function testUpdate()
	{
		$this->mock->shouldReceive('find')
				   ->with(1)
				   ->andReturn(m::mock(['update' => true]));

		$this->validate(true);
		$this->call('PATCH', 'matchreplay_chats/1');

		$this->assertRedirectedTo('matchreplay_chats/1');
	}

	public function testUpdateFails()
	{
		$this->mock->shouldReceive('find')->with(1)->andReturn(m::mock(['update' => true]));
		$this->validate(false);
		$this->call('PATCH', 'matchreplay_chats/1');

		$this->assertRedirectedTo('matchreplay_chats/1/edit');
		$this->assertSessionHasErrors();
		$this->assertSessionHas('message');
	}

	public function testDestroy()
	{
		$this->mock->shouldReceive('find')->with(1)->andReturn(m::mock(['delete' => true]));

		$this->call('DELETE', 'matchreplay_chats/1');
	}

	protected function validate($bool)
	{
		Validator::shouldReceive('make')
				->once()
				->andReturn(m::mock(['passes' => $bool]));
	}
}
