<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 09/01/14
 * Time: 22:21
 */

require_once("Fetcher.php");

$url = "http://public-yum.oracle.com/repo/OracleLinux/OL6/latest/x86_64/";
$outDir = "/Users/andy/Repos/oracle-6/base/";
//$url = "http://public-yum.oracle.com/repo/OracleLinux/OL6/UEKR3/latest/x86_64/";
//$outDir = "/Users/andy/Repos/oracle-6/uekr3/";

$fetcher = new Fetcher($url, $outDir);
$fetcher->run();

