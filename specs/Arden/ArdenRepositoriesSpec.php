<?php

require 'classes/Arden/Repositories.php';

class DescribeArdenRepositories extends \PHPSpec\Context
{
	public function itStoresRepositoriesForRetreival()
	{
		AutoModeler_Repositories::add('foo', 'bar');
		$this->spec(AutoModeler_Repositories::fetch('foo'))->should->be('bar');
	}
}
