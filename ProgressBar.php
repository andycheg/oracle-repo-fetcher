<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 10/01/14
 * Time: 01:40
 */

namespace Cli;
require_once("Cli.class.php");

use Cli;

class ProgressBar {
	protected $_leftBoundary = "[";
	protected $_rightBoundary = "]";
	protected $_marker = "#";
	protected $_empty = "-";

	private $_width;
	private $_leftMargin;
	private $_value = 0;
	private $_markerCount = 0;

	private $_onScreen = false;

	public function __construct($width, $leftMargin = 0)
	{
		$this->_width = $width;
		$this->_leftMargin = $leftMargin;
		$this->draw();
	}

	public function setValue($value)
	{
		if ($value < 0) $value = 0;
		if ($value > 1) $value = 1;

		$this->_value = $value;

		$markerCount = round($this->_value * $this->_width);

		if ($markerCount == $this->_markerCount)
			return;

		$this->_markerCount = $markerCount;
		$this->draw();
	}

	protected function draw()
	{
		if ($this->_onScreen)
			Cli::lineUp();

		if ($this->_leftMargin > 0)
			Cli::moveToColumn($this->_leftMargin);

		$this->printBar();
		$this->_onScreen = true;
		Cli::out("\n");
	}

	protected function printBar()
	{
		Cli::out($this->_leftBoundary);
		Cli::out(str_repeat($this->_marker, $this->_markerCount));
		Cli::out(str_repeat($this->_empty, $this->_width - $this->_markerCount));
		Cli::out($this->_rightBoundary);
	}
} 