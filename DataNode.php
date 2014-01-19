<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 09/01/14
 * Time: 22:52
 */

require_once("Downloader.php");
require_once("FileHooker.php");

use Curl\Downloader;

class DataNode {

	private $_type;
	private $_location;
	private $_checksum;
	private $_openChecksum;

	private $_url;
	private $_outDir;

	public function __construct(DOMNode $data, $url, $outDir)
	{
		if (is_null($data->attributes->getNamedItem("type")))
			throw new Exception("No type attribute in data node");

		$this->_type = $data->attributes->getNamedItem("type")->nodeValue;

		foreach ($data->childNodes as $childNode)
		{
			/** @var DOMNode childNode */
			switch ($childNode->nodeName)
			{
				case "location":
					$this->processLocationNode($childNode);
					break;
				case "checksum":
					$this->processChecksumNode($childNode, $this->_checksum);
					break;
				case "open-checksum":
					$this->processChecksumNode($childNode, $this->_openChecksum);
					break;
			}
		}

		$this->_url = $url;
		$this->_outDir = $outDir;
	}

	private function processLocationNode(DOMNode $node)
	{
		if (is_null($node->attributes->getNamedItem("href")))
			throw new Exception("No href attribute in location node");

		$this->_location = $node->attributes->getNamedItem("href")->nodeValue;
	}

	private function processChecksumNode(DOMNode $node, &$checksum)
	{
		if (is_null($node->attributes->getNamedItem("type")))
			throw new Exception("No type attribute in checksum node");

		if ($node->attributes->getNamedItem("type")->nodeValue != "sha")
			throw new Exception("Unexpected checksum type");

		$checksum = $node->nodeValue;
	}

	public function download()
	{
		$downloader = new Downloader($this->getFullLocation());
		$hooker = new FileHooker($this->getFilePath(), $this->_type);
		$downloader->download($hooker);
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;
	}

	public function getSize()
	{
		$downloader = new Downloader($this->getFullLocation());
		return $downloader->getSize();
	}

	/**
	 * @return string
	 */
	private function getFullLocation()
	{
		return $this->_url . "/" . $this->_location;
	}

	public function checkOnDisk()
	{
		if (file_exists($this->getFilePath()))
		{
			if ($this->fileChecksum() == $this->_checksum)
				return true;
			else
				return false;
		}
		/* elseif (file_exists($this->getOpenFilePath()))
		{
			if ($this->openFileChecksum() == $this->_openChecksum)
				return true;
			else
				return false;
		} */

		return false;
	}

	private function fileChecksum()
	{
		return sha1_file($this->getFilePath());
	}

	private function openFileChecksum()
	{
		return sha1_file($this->getOpenFilePath());
	}

	/**
	 * @return string
	 */
	private function getOpenFilePath()
	{
		if (preg_match('/^(.*)\\.gz$/', $this->getFilePath(), $parts))
			return $parts[1];

		return $this->getFilePath();
	}

	/**
	 * @return string
	 */
	private function getFilePath()
	{
		return $this->_outDir . "/" . $this->_location;
	}

	public function getFileUri()
	{
		if (file_exists($this->getOpenFilePath()))
			return "file://".$this->getOpenFilePath();

		else if (file_exists($this->getFilePath()))
		{
			if (preg_match('/^(.*)\\.gz$/', $this->getFilePath()))
				return "compress.zlib://file://".$this->getFilePath();
			else
				return "file://".$this->getFilePath();
		}

		return null;
	}

} 