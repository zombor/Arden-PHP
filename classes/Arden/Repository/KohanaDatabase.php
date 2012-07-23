<?php

class Arden_Repository_KohanaDatabase
{
	protected $_qb_classes = [
		'insert' => 'Database_Query_Builder_Insert',
	];

	public function __construct($database, $table_name = NULL)
	{
		$this->_database = $database;

		if ($table_name)
		{
			$this->_table_name = $table_name;
		}
	}

	public function create($object, $qb = NULL)
	{
		if ( ! $qb)
		{
			$qb = new $this->_qb_classes['insert'];
		}

		$reflection = new ReflectionClass($object);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
		$columns = [];
		$values = [];
		foreach ($properties as $p)
		{
			$columns[] = $p->getName();
			$values[] = $object->{$p->getName()};
		}

		$id = $qb->table($this->_table_name)->columns($columns)->values($values)->execute($this->_database);
		$object->id = $id[0];

		return $object;
	}

	public function update($object, $qb = NULL)
	{
		$reflection = new ReflectionClass($object);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
		$set = [];
		foreach ($properties as $p)
		{
			$set[$p->getName()] = $object->{$p->getName()};
		}

		$updated = $qb->table($this->_table_name)->set($set)->execute($this->_database);

		return $object;
	}
}
