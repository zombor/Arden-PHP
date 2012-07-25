<?php

class Arden_Repository_KohanaDatabase
{
	protected $_qb_insert;
	protected $_qb_update;
	protected $_qb_delete;

	public function __construct($database, $qb_insert, $qb_update, $qb_delete, $table_name = NULL)
	{
		$this->_database = $database;

		if ($table_name)
		{
			$this->_table_name = $table_name;
		}

		$this->_qb_insert = $qb_insert;
		$this->_qb_update = $qb_update;
		$this->_qb_delete = $qb_delete;
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

	public function delete($object)
	{
		return (bool) $this->_qb_delete->table($this->_table_name)->where('id', '=', $object->id)->execute($this->_database);
	}
}
