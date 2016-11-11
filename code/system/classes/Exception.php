<?php

/**
* WizyTówka 5
* Content management system own exception.
*/
namespace WizyTowka;

class Exception extends \Exception
{
	public function __construct()
	{
		if (func_num_args() < 2) {
			throw new Exception('Exception must have code.', 0);
		}

		call_user_func_array([parent::class, '__construct'], func_get_args());
	}
}