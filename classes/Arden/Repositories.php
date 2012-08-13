<?php

class Arden_Repositories
{
	protected static $_repositories = array();

	public static function add($key, $repository)
	{
		static::$_repositories[$key] = $repository;
	}

	public static function fetch($key)
	{
		return static::$_repositories[$key];
	}
}
