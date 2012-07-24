<?php

class Arden_Repository_KohanaDatabase
{
	protected $_qb_insert;
	protected $_qb_update;

	public function __construct($database, $qb_insert, $qb_update, $table_name = NULL)
	{
		$this->_database = $database;

		if ($table_name)
		{
			$this->_table_name = $table_name;
		}

		$this->_qb_insert = $qb_insert;
		$this->_qb_update = $qb_update;
	}

	public function create($object)
	{
		$reflection = new ReflectionClass($object);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
		$columns = [];
		$values = [];
		foreach ($properties as $p)
		{
			$columns[] = $p->getName();
			$values[] = $object->{$p->getName()};
		}

		$id = $this->_qb_insert->table($this->_table_name)->columns($columns)->values($values)->execute($this->_database);
		$object->id = $id[0];

		return $object;
	}

	public function update($object)
	{
		$reflection = new ReflectionClass($object);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
		$set = [];
		foreach ($properties as $p)
		{
			$set[$p->getName()] = $object->{$p->getName()};
		}

		$updated = $this->_qb_update->table($this->_table_name)->set($set)->execute($this->_database);

		return $object;
	}
}
