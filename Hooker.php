<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 10/01/14
 * Time: 02:13
 */

namespace Curl;


interface Hooker {

	public function onInit();
	public function onProgress($fullSize, $completed);
	public function onWrite($data);
	public function onFinish();

} 