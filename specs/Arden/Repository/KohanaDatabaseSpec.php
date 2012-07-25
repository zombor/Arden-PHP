<?php

require 'classes/Arden/Repository/KohanaDatabase.php';
require 'classes/Arden/InvalidObjectException.php';

class DescribeKohanaDatabase extends \PHPSpec\Context
{
	public function before()
	{
		$this->qb_select = Mockery::mock('qb_select');
		$this->qb_insert = Mockery::mock('qb_insert');
		$this->qb_update = Mockery::mock('qb_update');
		$this->qb_delete = Mockery::mock('qb_delete');
		$this->database = Mockery::mock('Database');
		$this->repo = new Arden_Repository_KohanaDatabase(
			$this->database,
			$this->qb_select,
			$this->qb_insert,
			$this->qb_update,
			$this->qb_delete,
			'Model_User',
			'users'
		);
	}

	public function itLoadsASingleObject()
	{
		$saved_user = new Model_User(1, 'foo@bar.com');
		$results = Mockery::mock('result');
		$results->shouldReceive('current')->once()->andReturn($saved_user);
		$this->qb_select->shouldReceive('from')
			->once()
			->with('users')
			->andReturn($this->qb_select);
		$this->qb_select->shouldReceive('where')
			->once()
			->with('id', '=', 1);
		$this->qb_select->shouldReceive('as_object')
			->once()
			->with('Model_User');
		$this->qb_select->shouldReceive('execute')
			->once()
			->with($this->database)
			->andReturn($results);

		$user = $this->repo->load_object(['id' => 1]);
		$this->spec($user)->should->be($saved_user);
	}

	public function itReturnsNullWhenNoObjectIsFound()
	{
		$results = Mockery::mock('result');
		$results->shouldReceive('current')->once()->andReturn(NULL);
		$this->qb_select->shouldReceive('from')
			->once()
			->with('users')
			->andReturn($this->qb_select);
		$this->qb_select->shouldReceive('where')
			->once()
			->with('id', '=', 1);
		$this->qb_select->shouldReceive('as_object')
			->once()
			->with('Model_User');
		$this->qb_select->shouldReceive('execute')
			->once()
			->with($this->database)
			->andReturn($results);

		$user = $this->repo->load_object(['id' => 1]);
		$this->spec($user)->should->beNull();
	}

	public function itLoadsMultipleObjects()
	{
		$user1 = new Model_User(1, 'foo@bar.com');
		$user2 = new Model_User(2, 'foo@bar.com');

		$results = [$user1, $user2];
		$this->qb_select->shouldReceive('from')
			->once()->with('users');
		$this->qb_select->shouldReceive('as_object')
			->once()->with('Model_User');
		$this->qb_select->shouldReceive('where')
			->once()->with('id', '=', 1);
		$this->qb_select->shouldReceive('where')
			->once()->with('id', '=', 2);
		$this->qb_select->shouldReceive('execute')
			->once()->with($this->database)
			->andReturn($results);



		$users = $this->repo->load_set([
			['id' => 1],
			['id' => 2],
		]);
		$this->spec($users)->should->be(
			[
				$user1, $user2
			]
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

	public function itRaisesAnExceptionWhenInsertingObjectWithId()
	{
		$user = new Model_User(1, 'foo@bar.com');
		$this->spec(
			function() use($user)
			{
				$this->repo->create($user);
			}
		)->should->throwException('Arden_InvalidObjectException');
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

	public function itRaisesAnExceptionWhenUpdatingObjectWithNoId()
	{
		$user = new Model_User(NULL, 'foo@bar.com');
		$this->spec(
			function() use($user)
			{
				$this->repo->update($user);
			}
		)->should->throwException('Arden_InvalidObjectException');
	}

	public function itDeletesALoadedRecord()
	{
		$user = new Model_User(1, 'foo@bar.com');
		$this->qb_delete
			->shouldReceive('table')
			->once()
			->with('users')
			->andReturn($this->qb_delete);
		$this->qb_delete
			->shouldReceive('where')
			->once()
			->with('id', '=', 1)
			->andreturn($this->qb_delete);
		$this->qb_delete
			->shouldReceive('execute')
			->once()
			->with($this->database)
			->andReturn(1);

		$this->spec($this->repo->delete($user))->should->be(TRUE);
	}

	public function itRaisesAnExceptionWhenDeletingObjectWithNoId()
	{
		$user = new Model_User(NULL, 'foo@bar.com');
		$this->spec(
			function() use($user)
			{
				$this->repo->delete($user);
			}
		)->should->throwException('Arden_InvalidObjectException');
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
