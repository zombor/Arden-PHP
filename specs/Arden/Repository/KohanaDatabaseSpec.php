<?php

require 'classes/Arden/Repository/KohanaDatabase.php';

class DescribeKohanaDatabase extends \PHPSpec\Context
{
	public function itCreatesARecordFromAnUnloadedObject()
	{
		$user = new Model_User(NULL, 'foo@bar.com');
		$database = Mockery::mock('alias:Database');
		$qb = Mockery::mock('qb');
		$qb->shouldReceive('table')->with('users')->andReturn($qb);
		$qb->shouldReceive('columns')->with(['id', 'email'])->andReturn($qb);
		$qb->shouldReceive('values')->with([NULL, 'foo@bar.com'])->andReturn($qb);
		$qb->shouldReceive('execute')->with($database)->andReturn([1, 1]);

		$repo = new Arden_Repository_KohanaDatabase($database, 'users');
		$new_user = $repo->create($user, $qb);

		$this->spec($new_user->id)->should->be(1);
	}

	public function itUpdatesARecordFromALoadedObject()
	{
		$user = new Model_User(1, 'foo@bar.com');
		$this->pending();
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
