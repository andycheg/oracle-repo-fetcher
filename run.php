<?php
require_once("Fetcher.php");


$repoUrl = $argv[1];
$outDir = $argv[2];

try
{
	if (empty($repoUrl))
		throw new Exception("Repository URL is empty");

	if (empty($outDir))
		throw new Exception("Output directory is empty");

	if (!is_dir($outDir))
		throw new Exception("'$outDir' is not a directory");
}
catch (Exception $e)
{
	echo "Usage: {$argv[0]} <repo-url> <output-dir>\n\n";
	echo $e->getMessage();
	echo "\n";
}

$fetcher = new Fetcher($repoUrl, $outDir);
$fetcher->run();
