<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 10/01/14
 * Time: 02:20
 */
require_once("Hooker.php");
require_once("ProgressBar.php");

use Cli\ProgressBar;

class FileHooker implements \Curl\Hooker {

	private $_label;
	private $_fileName;
	private $_fd;

	/**
	 * @var ProgressBar
	 */
	private $_bar;

	function __construct($fileName, $label)
	{
		$this->_fileName = $fileName;
		$this->_label = $label;
	}


	public function onInit()
	{
		echo $this->_label;
		$this->_bar = new ProgressBar(80, 20);
		$directory = dirname($this->_fileName);
		if (!file_exists($directory))
			mkdir($directory, 0777, true);
		$this->_fd = fopen($this->_fileName, "w");
	}

	public function onProgress($fullSize, $completed)
	{
		$this->_bar->setValue($completed / $fullSize);
	}

	public function onWrite($data)
	{
		if (strlen($data) > 0)
			fwrite($this->_fd, $data);
	}

	public function onFinish()
	{
		fclose($this->_fd);
		$this->_bar = null;
	}

} 