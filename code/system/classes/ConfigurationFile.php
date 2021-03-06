<?php

/**
* WizyTówka 5
* Class for various configurations stored in JSON files.
*/
namespace WizyTowka;

class ConfigurationFile implements \IteratorAggregate, \Countable
{
	static private $_configurationFiles = [];

	private $_fileName;
	private $_configuration = [];
	private $_wasChanged = false;
	private $_readOnly = false;

	public function __construct(string $fileName, bool $readOnly = false)
	{
		$this->_fileName = (string)$fileName;
		$this->_readOnly = (boolean)$readOnly;

		// It is a hash of full path of configuration file.
		// We should avoid realpath() when it is possible to limit operations on file system.
		$fileNameHash = md5(($fileName[0] == '/') ? $fileName : realpath($fileName));

		$readingFirstTime = !isset(self::$_configurationFiles[$fileNameHash]);

		if ($readingFirstTime) {
			self::$_configurationFiles[$fileNameHash] = [];
		}

		// If there is more than one instance of ConfigurationFile class that opens the same file,
		// configuration changes will not be overwritten and each instance of ConfigurationFile class
		// will use current configuration without reading file from file system more than once.
		$this->_configuration =& self::$_configurationFiles[$fileNameHash];

		if ($readingFirstTime) {
			$this->refresh();
		}
	}

	public function __destruct()
	{
		$this->save();
	}

	public function __get(string $key)
	{
		return $this->_configuration[$key];
	}

	public function __set(string $key, $value) : void
	{
		if ($this->_readOnly) {
			throw ConfigurationFileException::writingWhenReadOnly($this->_fileName);
		}

		$this->_wasChanged = true;
		$this->_configuration[$key] = $value;
	}

	public function __isset(string $key) : bool
	{
		return isset($this->_configuration[$key]);
	}

	public function __unset(string $key) : void
	{
		if ($this->_readOnly) {
			throw ConfigurationFileException::writingWhenReadOnly($this->_fileName);
		}

		$this->_wasChanged = true;
		unset($this->_configuration[$key]);
	}

	public function __debugInfo() : array
	{
		return $this->_configuration;
	}

	public function getIterator() : iterable // For IteratorAggregate interface.
	{
		foreach ($this->_configuration as $key => $value) {
			yield $key => $value;
		}
	}

	public function count() : int  // For Countable interface.
	{
		return count($this->_configuration);
	}

	public function refresh() : void
	{
		$configuration = json_decode(file_get_contents($this->_fileName), true);  // "true" means associative array.

		if (json_last_error() != JSON_ERROR_NONE) {
			throw ConfigurationFileException::JSONError($this->_fileName);
		}
		if (!is_array($configuration)) {
			throw ConfigurationFileException::invalidArray($this->_fileName);
		}

		$this->_configuration = $configuration;
	}

	public function save() : void
	{
		if ($this->_wasChanged) {
			file_put_contents(
				$this->_fileName,
				json_encode(
					$this->_configuration,
					JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
				),
				LOCK_EX
			);

			if (json_last_error() != JSON_ERROR_NONE) {
				throw ConfigurationFileException::JSONError($this->_fileName);
			}
		}
	}

	static public function createNew(string $fileName) : void
	{
		file_put_contents($fileName, json_encode([]));
	}
}

class ConfigurationFileException extends Exception
{
	static public function JSONError($fileName)
	{
		return new self('Error "' . json_last_error_msg() . '" during JSON operation on configuration file: ' . $fileName . '.', 1);
	}
	static public function invalidArray($fileName)
	{
		return new self('Configuration file ' . $fileName . ' does not contain array.', 2);
	}
	static public function writingWhenReadOnly($fileName)
	{
		return new self('Configuration file ' . $fileName . ' is opened as read only.', 3);
	}
}