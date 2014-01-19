<?php
/**
 *   $Id$
 * 
 *   Класс для работы с выводом в консоли
 * 
 *   AUTHOR: Chegodaev Andrey <chegodaev@tutu.ru>
 *
 */

class Cli
{
	const RED='red';
	const GREEN='green';
	const BLUE='blue';
	const YELLOW='yellow';
	const MAGENTA='magenta';

	protected static $_colors = [
		'yellow'    => "\033[01;33m",
		'green'     => "\033[01;32m",
		'red'       => "\033[01;31m",
		'blue'      => "\033[01;34m",
		'magenta'	=> "\033[01;35m",
	];

	protected static $_textOptions = [
		'reset'		=> "\033[00;00m",
		'boldStart'	=> "\033[1m",
		"boldEnd"	=> "\033[0m",
	];

	/**
	 * @param string $msg
	 * @param string $color
	 * @param bool $underline
	 * @param bool $blink
	 */
	public static function out($msg, $color = NULL, $underline = false, $blink = false)
	{
		$resetSeq = self::$_textOptions['reset'];
		$underlineSeq = "\033[04m";
		$blinkSeq = "\033[05m";

		$needResetSeq = false;
		if (!is_null($color) && array_key_exists($color, self::$_colors))
		{
			echo self::$_colors[$color];
			$needResetSeq = true;
		}

		if ($underline)
		{
			echo $underlineSeq;
			$needResetSeq = true;
		}

		if($blink)
		{
			echo $blinkSeq;
			$needResetSeq = true;
		}

		echo "$msg";

		if ($needResetSeq)
			echo $resetSeq;
	}

	public static function eraseLine()
	{
		echo "\033[2K";
	}

	public static function lineUp()
	{
		echo "\033[1F";
	}

	public static function lineDown()
	{
		echo "\033[1E";
	}

	public static function rememberPosition()
	{
		echo "\033[s";
	}

	public static function restorePosition()
	{
		echo "\033[u";
	}

	/**
	 * @param string $column
	 */
	public static function moveToColumn($column)
	{
		echo "\033[{$column}G";
	}

	/**
	 * @param string $question
	 * @param string $secondOption
	 * @return bool
	 */
	public static function confirm ($question, $secondOption = "n")
	{
		do
		{
			$selection = strtolower(readline("$question [y/{$secondOption}]: "));
		}	while (!in_array($selection, array('y',$secondOption)));
		return ($selection == 'y');
	}

	/**
	 * @param string $color
	 * @return string
	 */
	public static function getColor ($color)
	{
		return self::$_colors[$color];
	}

	/**
	 * @return string
	 */
	public static function getReset ()
	{
		return self::$_textOptions['reset'];
	}

	/**
	 * @return string
	 */
	public static function getBoldStart ()
	{
		return self::$_textOptions['boldStart'];
	}

	/**
	 * @return string
	 */
	public static function getBoldEnd ()
	{
		return self::$_textOptions['boldEnd'];
	}

	/**
	 * @return string
	 */
	public static function checked ()
	{
		return "[√]";
	}

	/**
	 * @return string
	 */
	public static function unchecked ()
	{
		return "[×]";
	}
}