<?php

/**
* WizyTówka 5
* Admin page — search engines settings settings.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class SearchSettings extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Informacje wyszukiwarek';
	protected $_userRequiredPermissions = WT\User::PERM_EDITING_SITE_CONFIG;

	private $_settings;

	private $_robotsSettings = [];

	protected function _prepare()
	{
		$this->_settings = WT\Settings::get();

		$this->_robotsSettings = array_map('strtolower', array_map('trim',
			explode(',', $this->_settings->searchEnginesRobots)
		));
	}

	public function POSTQuery()
	{
		$this->_settings->searchEnginesDescription = str_replace("\n", ' ', $_POST['searchEnginesDescription']);

		foreach (['noindex', 'noimageindex', 'noarchive', 'nofollow'] as $option) {
			if (isset($_POST['robots'][$option])) {
				$this->_robotsSettings[] = $option;
			}
			else {
				$this->_robotsSettings = array_diff($this->_robotsSettings, [$option]);
			}
		}
		$this->_settings->searchEnginesRobots = implode(', ', array_filter(array_unique(
			array_diff($this->_robotsSettings, ['index', 'follow'])
		)));

		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		$this->_HTMLTemplate->settings = $this->_settings;

		$this->_HTMLTemplate->robots = [
			'noindex'      => in_array('noindex',      $this->_robotsSettings),
			'noimageindex' => in_array('noimageindex', $this->_robotsSettings),
			'noarchive'    => in_array('noarchive',    $this->_robotsSettings),
			'nofollow'     => in_array('nofollow',     $this->_robotsSettings),
		];
	}
}