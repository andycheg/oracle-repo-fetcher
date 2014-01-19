<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 10/01/14
 * Time: 22:17
 */

require_once ("PackageMeta.php");

class PackageList implements Iterator, Countable
{
	private $_fileUri;

	private $_opened = false;

	/**
	 * @var XMLReader
	 */
	private $_reader;

	private $_count;
	private $_index;

	private $_valid;

	public function __construct(DataNode $packageNode)
	{
		$this->_fileUri = $packageNode->getFileUri();
		$this->rewind();
	}

	private function reader()
	{
		if (is_null($this->_reader))
		{
			$this->_reader = new XMLReader();
			$this->_reader->open($this->_fileUri);

			if (!$this->init())
				throw new Exception("Cannot init reading repodata");

			$this->_opened = true;
		}

		return $this->_reader;
	}

	private function init()
	{
		if (!$this->_reader->read())
			return false;

		if ($this->_reader->nodeType != XMLReader::ELEMENT)
			return false;

		if ($this->_reader->name != "metadata")
			return false;

		$this->_count = $this->_reader->getAttribute("packages");

		if (is_null($this->_count))
			return false;

		if (!$this->readToNextPackage())
			return false;

		$this->_index = 0;
		$this->_valid = true;

		return true;
	}

	private function readToNextPackage()
	{
		if ($this->placedOnPackage())
			return $this->_reader->next("package");

		while ($this->_reader->read())
		{
			if ($this->placedOnPackage())
				return true;
		}
		return false;
	}

	private function packageFactory(DOMNode $packageNode)
	{
		return new PackageMeta($packageNode);
	}

	public function count()
	{
		return $this->_opened ? $this->_count : 0;
	}

	/**
	 * @return PackageMeta
	 */
	public function current()
	{
		if (!$this->_opened || !$this->_valid)
			return null;

		$packageNode = $this->_reader->expand();
		return $this->packageFactory($packageNode);
	}

	public function key()
	{
		return $this->_opened && $this->_valid ? $this->_index : null;
	}

	public function next()
	{
		if (!$this->_opened)
			return;

		if (!$this->readToNextPackage())
			$this->_valid = false;

		$this->_index++;
	}

	public function valid()
	{
		return $this->_opened && $this->_valid;
	}

	public function rewind()
	{
		if ($this->_valid && $this->_index == 0)
			return;

		if ($this->_opened)
		{
			$this->reader()->close();
			$this->_reader = null;
		}

		$this->reader();
	}

	/**
	 * @return bool
	 */
	private function placedOnPackage()
	{
		return $this->_reader->nodeType == XMLReader::ELEMENT && $this->_reader->name == "package";
	}


}