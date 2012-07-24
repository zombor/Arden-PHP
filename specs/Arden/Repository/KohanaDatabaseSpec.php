<?php

require 'classes/Arden/Repository/KohanaDatabase.php';

class DescribeKohanaDatabase extends \PHPSpec\Context
{
	public function before()
	{
		$this->qb_insert = Mockery::mock('qb_insert');
		$this->qb_update = Mockery::mock('qb_update');
		$this->database = Mockery::mock('Database');
		$this->repo = new Arden_Repository_KohanaDatabase(
			$this->database,
			$this->qb_insert,
			$this->qb_update,
			'users'
		);
	}

	public function itCreatesARecordFromAnUnloadedObject()
	{
		$user = new Model_User(NULL, 'foo@bar.com');
		$this->qb_insert
			->shouldReceive('table')
			->once()
			->with('users')
			->andReturn($this->qb_insert);
		$this->qb_insert
			->shouldReceive('columns')
			->once()
			->with(['id', 'email'])
			->andReturn($this->qb_insert);
		$this->qb_insert
			->shouldReceive('values')
			->once()
			->with([NULL, 'foo@bar.com'])
			->andReturn($this->qb_insert);
		$this->qb_insert
			->shouldReceive('execute')
			->once()
			->with($this->database)->andReturn([1, 1]);

		$new_user = $this->repo->create($user);

		$this->spec($new_user->id)->should->be(1);
	}

	public function itUpdatesARecordFromALoadedObject()
	{
		$user = new Model_User(1, 'foo@bar.com');
		$this->qb_update
			->shouldReceive('table')
			->once()
			->with('users')
			->andReturn($this->qb_update);
		$this->qb_update
			->shouldReceive('set')
			->once()
			->with(['id' => 1, 'email' => 'foo@bar.com'])
			->andReturn($this->qb_update);
		$this->qb_update
			->shouldReceive('execute')
			->once()
			->with($this->database)
			->andReturn(1);

		$new_user = $this->repo->update($user);
	}
}

class Model_User {
	public $id;
	public $email;

	public function __construct($id, $email)
	{
		$this->id = $id;
		$this->email = $email;
	}
}
