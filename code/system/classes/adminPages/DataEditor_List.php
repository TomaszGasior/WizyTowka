<?php

/**
* WizyTówka 5
* Admin page — data editor, file list.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class DataEditor_List extends WT\AdminPanel
{
	protected $_pageTitle = 'Edytor plików';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}