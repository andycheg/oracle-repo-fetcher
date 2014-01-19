<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 10/01/14
 * Time: 02:05
 */

namespace Curl;

require_once("Hooker.php");

class Downloader {

	private $_url;
	/**
	 * @var Hooker
	 */
	private $_hooker;

	public function __construct ($url)
	{
		$this->_url = $url;
	}

	public function getSize()
	{
		$ch = curl_init($this->_url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

		curl_exec($ch);
		$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		curl_close($ch);

		return $size;
	}

	public function download(Hooker $hooker)
	{
		$ch = curl_init($this->_url);

		$this->_hooker = $hooker;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_BUFFERSIZE, 4096);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_NOPROGRESS, false);
		curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'progress']);
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, [$this, 'write']);

		$this->_hooker->onInit();
		curl_exec($ch);
		$this->_hooker->onFinish();
		$this->_hooker = null;

		curl_close($ch);
	}

	public function progress($size, $downloaded,
		/** @noinspection PhpUnusedParameterInspection */ $us, /** @noinspection PhpUnusedParameterInspection */ $u)
	{
		if ($size == 0)
			return;

		if (!is_null($this->_hooker))
			$this->_hooker->onProgress($size, $downloaded);
	}


	public function write(/** @noinspection PhpUnusedParameterInspection */ $curl, $data)
	{
		if (!is_null($this->_hooker))
			$this->_hooker->onWrite($data);

		return strlen($data);
	}

} 