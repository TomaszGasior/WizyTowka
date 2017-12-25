<?php

/**
* WizyTówka 5
* Admin page — create user.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class UserCreate extends WT\AdminPanelPage
{
	use UserEditCreateCommon;

	protected $_pageTitle = 'Utwórz użytkownika';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	public function POSTQuery()
	{
		$user = new WT\User;

		if (empty($_POST['name'])) {
			$this->_HTMLMessage->error('Nie określono nazwy użytkownika.');
			return;
		}
		elseif (!$this->_checkUserName($_POST['name'])) {
			$this->_HTMLMessage->error('Podana nazwa użytkownika jest niepoprawna.');
			return;
		}
		elseif (WT\User::getByName($_POST['name'])) {
			$this->_HTMLMessage->error('Nazwa użytkownika „' . $_POST['name'] . '” jest zajęta.');
			return;
		}
		else {
			$user->name = $_POST['name'];
		}

		if (empty($_POST['passwordText_1']) or empty($_POST['passwordText_2'])) {
			$this->_HTMLMessage->error('Nie określono hasła użytkownika.');
			return;
		}
	    elseif ($_POST['passwordText_1'] === $_POST['passwordText_2']) {
			$user->setPassword($_POST['passwordText_1']);
		}
		else {
			$this->_HTMLMessage->error('Podane hasła nie są identyczne.');
			return;
		}

		$user->permissions = $this->_calculatePermissionValueFromNamesArray(isset($_POST['permissions']) ? $_POST['permissions'] : []);

		$user->save();
		$this->_redirect('Users', ['msg' => 1]);
	}

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('UserEditCreate');
		$this->_HTMLTemplate->createInsteadEdit = true;

		$this->_HTMLTemplate->permissions = $this->_prepareNamesArrayFromPermissionValue(0);
	}
}