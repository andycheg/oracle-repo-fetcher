<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 11/01/14
 * Time: 15:13
 */

class PackageMeta {

	private $_name;
	private $_location;
	private $_arch;
	private $_size;

	public function __construct(DOMNode $packageNode)
	{
		foreach ($packageNode->childNodes as $childNode)
		{
			/** @var DOMNode $childNode */
			switch ($childNode->nodeName)
			{
				case "name":
					$this->_name = $childNode->nodeValue;
					break;
				case "arch":
					$this->_arch = $childNode->nodeValue;
					break;
				case "location":
					$this->processLocationNode($childNode);
					break;
				case "size":
					$this->processSizeNode($childNode);
					break;
			}
		}
	}

	/**
	 * @return string
	 */
	public function getArch()
	{
		return $this->_arch;
	}

	/**
	 * @return string
	 */
	public function getLocation()
	{
		return $this->_location;
	}

	private function processLocationNode(DOMNode $node)
	{
		if (is_null($node->attributes->getNamedItem("href")))
			throw new Exception("No href attribute in location node");

		$this->_location = $node->attributes->getNamedItem("href")->nodeValue;
	}

	private function processSizeNode(DOMNode $node)
	{
		if (is_null($node->attributes->getNamedItem("package")))
			throw new Exception("No package attribute in size node");

		$this->_size = $node->attributes->getNamedItem("package")->nodeValue;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @return mixed
	 */
	public function getSize()
	{
		return $this->_size;
	}

}