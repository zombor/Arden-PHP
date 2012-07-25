<?php

class Arden_Repository_KohanaDatabase
{
	protected $_model_class;

	protected $_qb_select;
	protected $_qb_insert;
	protected $_qb_update;
	protected $_qb_delete;

	public function __construct($database, $qb_select, $qb_insert, $qb_update, $qb_delete, $model_class = NULL, $table_name = NULL)
	{
		$this->_database = $database;

		if ($table_name)
		{
			$this->_table_name = $table_name;
		}

		if ($model_class)
		{
			$this->_model_class = $model_class;
		}

		$this->_qb_select = $qb_select;
		$this->_qb_insert = $qb_insert;
		$this->_qb_update = $qb_update;
		$this->_qb_delete = $qb_delete;
	}

	public function load_object(array $parameters)
	{
		$this->_qb_select->from($this->_table_name);
		$this->_qb_select->as_object($this->_model_class);
		foreach ($parameters as $column => $value)
		{
			$this->_qb_select->where($column, '=', $value);
		}

		return $this->_qb_select->execute($this->_database)->current();
	}

	public function create($object)
	{
		if ($object->id)
		{
			throw new Arden_InvalidObjectException('Cannot create a loaded object');
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

		$id = $this->_qb_insert->table($this->_table_name)->columns($columns)->values($values)->execute($this->_database);
		$object->id = $id[0];

		return $object;
	}

	public function update($object)
	{
		if ( ! $object->id)
		{
			throw new Arden_InvalidObjectException('Cannot update a non-loaded object');
		}

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
		if ( ! $object->id)
		{
			throw new Arden_InvalidObjectException('Cannot delete a non-loaded object');
		}

		return (bool) $this->_qb_delete->table($this->_table_name)->where('id', '=', $object->id)->execute($this->_database);
	}
}
