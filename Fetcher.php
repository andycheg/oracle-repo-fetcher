<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 09/01/14
 * Time: 22:21
 */
require_once("DataNode.php");
require_once("PackageList.php");
require_once("Package.php");

class Fetcher {

	private $_url;
	private $_outdir;

	const REPODATA = "repodata";
	const REPOMD = "repomd.xml";

	const PRIMARY_TYPE = "primary";

	private $_archsToProcess = ['x86_64', 'noarch'];
	private $_repoCount;
	private $_processCount;
	private $_downloadCount;
	private $_onDiskCount;

	private $_downloadSize;


	/**
	 * @var Package[]
	 */
	private $_packages = [];


	public function __construct($url, $outdir)
	{
		$this->_url = $url;
		$this->_outdir = $outdir;
	}

	public function run()
	{
		$repoDataDir = $this->_outdir . "/" . self::REPODATA;
		if (file_exists($repoDataDir))
		{
			if (!is_dir($repoDataDir))
				throw new Exception("$repoDataDir exists and isn't directory");
		}
		else
		{
			mkdir($repoDataDir);
		}

		$repomd = file_get_contents($this->_url . "/" . self::REPODATA . "/" . self::REPOMD);
		if ($repomd === false)
			throw new Exception("Cannot fetch repomd.xml");

//		$repomd = file_get_contents($repoDataDir . "/" . self::REPOMD);
		file_put_contents($repoDataDir . "/" . self::REPOMD, $repomd);

		$dom = new DOMDocument();
		$dom->loadXML($repomd);
		$primaryNode = null;
		foreach ($dom->getElementsByTagName("data") as $dataNode)
		{
			$data = new DataNode($dataNode, $this->_url, $this->_outdir);
			if (!$data->checkOnDisk())
				$data->download($this->_outdir);

			if ($data->getType() == self::PRIMARY_TYPE)
				$primaryNode = $data;
		}

		if (is_null($primaryNode))
		{
			Cli::out("No primary repo file",Cli::RED);
			exit;
		}

		$list = new PackageList($primaryNode);
		foreach ($list as $key => $packageMeta)
		{
			/** @var PackageMeta $packageMeta */
			$this->_packages[] = new Package($packageMeta, $this->_outdir, $this->_url);
		}

		$this->_repoCount = count($this->_packages);

		Cli::out("Repository contains ");
		Cli::out($this->_repoCount,Cli::GREEN);
		Cli::out(" packages\n");

		foreach ($this->_packages as $key => $package)
		{
			if (!in_array($package->getMeta()->getArch(), $this->_archsToProcess))
				unset($this->_packages[$key]);
		}

		$this->_processCount = count($this->_packages);

		Cli::out("We will process only ");
		Cli::out($this->_processCount,Cli::GREEN);
		Cli::out(" packages for archs [");
		Cli::out(implode(" ", $this->_archsToProcess), Cli::BLUE);
		Cli::out("]\n");

		$this->_downloadSize = 0;
		foreach ($this->_packages as $key => $package)
		{
			if ($package->onDisk())
				unset($this->_packages[$key]);
			else
				$this->_downloadSize += $package->getMeta()->getSize();
		}

		$this->_downloadCount = count($this->_packages);
		$this->_onDiskCount = $this->_processCount - $this->_downloadCount;

		Cli::out("There are already ");
		Cli::out($this->_onDiskCount,Cli::GREEN);
		Cli::out(" packages on disk\nWe will download ");
		Cli::out($this->_downloadCount, Cli::GREEN);
		Cli::out(" packages\n");
		Cli::out("Download size is ");
		Cli::out(number_format($this->_downloadSize), Cli::GREEN);
		Cli::out(" bytes\n\n");

		$index = 1;
		foreach($this->_packages as $package)
		{
			$label =  "$index/".$this->_downloadCount;
			$package->download($label);
			$index++;
		}
	}



} 