<?php

require 'classes/Arden/Repositories.php';

class DescribeArdenRepositories extends \PHPSpec\Context
{
	public function itStoresRepositoriesForRetreival()
	{
		Arden_Repositories::add('foo', 'bar');
		$this->spec(Arden_Repositories::fetch('foo'))->should->be('bar');
	}
}
