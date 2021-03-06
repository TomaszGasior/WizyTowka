<?php

/**
* WizyTówka 5
* Common code for UserEdit and UserCreate controllers.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

trait UserEditCreateCommon
{
	private function _checkUserName(string $name) : bool
	{
		return preg_match('/^[a-zA-Z0-9_\-.]+$/', $name);
	}

	// Takes current value of User::$permissions field. Returns array with permissions names (User::PERM_* constants names
	// without PERM_ prefix) as keys and true or false (which defines whether user has permission) as values.
	private function _prepareNamesArrayFromPermissionValue(int $currentPermissionsValue) : array
	{
		$possibleUserPermissions = array_filter(
			(new \ReflectionClass(__\User::class))->getConstants(),
			function($constantName){ return (strpos($constantName, 'PERM_') === 0); },
			ARRAY_FILTER_USE_KEY
		);

		$namedPermissions = [];
		foreach ($possibleUserPermissions as $constantNameFull => $permissionValue) {
			$constantNamePart = str_replace('PERM_', null, $constantNameFull);
			$namedPermissions[$constantNamePart] = (bool)($currentPermissionsValue & $permissionValue);
		}
		return $namedPermissions;
	}

	// Takes array prepared by method above. Returns value for User::$permission field.
	private function _calculatePermissionValueFromNamesArray(array $currentNamedPermissions) : int
	{
		$permisionsValue = 0;
		foreach ($currentNamedPermissions as $constantNamePart => $permissionEnabled) {
			if ($permissionEnabled) {
				$permisionsValue = $permisionsValue | constant(__\User::class . '::PERM_' . $constantNamePart);
			}
		}
		return $permisionsValue;
	}
}