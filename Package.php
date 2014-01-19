<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 11/01/14
 * Time: 16:16
 */

use Curl\Downloader;

class Package {

	/**
	 * @var PackageMeta
	 */
	private $_meta;

	private $_outDir;
	private $_repoUrl;

	function __construct($meta, $outDir, $repoUrl)
	{
		$this->_meta = $meta;
		$this->_outDir = $outDir;
		$this->_repoUrl = $repoUrl;
	}

	/**
	 * @return string
	 */
	private function getFilePath()
	{
		return $this->_outDir . "/" . $this->_meta->getLocation();
	}

	/**
	 * @return string
	 */
	private function getUrl()
	{
		return $this->_repoUrl . "/" . $this->_meta->getLocation();
	}

	public function onDisk()
	{
		if (!file_exists($this->getFilePath()))
			return false;

		if (!is_file($this->getFilePath()))
			return false;

		$sizeOnDisk = filesize($this->getFilePath());
		if ($sizeOnDisk != $this->_meta->getSize())
			return false;

		return true;
	}

	/**
	 * @return \PackageMeta
	 */
	public function getMeta()
	{
		return $this->_meta;
	}

	public function download($label)
	{
		$downloader = new Downloader($this->getUrl());
		$hooker = new FileHooker($this->getFilePath(), $label);
		$downloader->download($hooker);
	}


} 