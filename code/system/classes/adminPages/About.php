<?php

/**
* WizyTówka 5
* Admin page — about system.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class About extends WT\AdminPanel
{
	protected $_pageTitle = 'Informacje o systemie';

	protected function _output()
	{
		$this->_apTemplate->version = WT\VERSION;
		$this->_apTemplate->releaseDate = WT\VERSION_DATE;
		$this->_apTemplate->betaVersionWarning = !WT\VERSION_STABLE;
	}
}