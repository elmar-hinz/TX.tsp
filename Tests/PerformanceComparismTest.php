<?php

namespace ElmarHinz\Tests;

require_once("vendor/autoload.php");

use \ElmarHinz\TypoScriptParser as NonRecursiveParser;
use \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser as RecursiveParser;

define("LF", "\n");
define("TAB", "\t");

class PerformanceComparismTest
{

	public function main()
	{
		$provider = "simpleProvider";
		$times = 100;
		$results[$times] = $this->recursiveVsNonrecursive($provider, $times);
		$times = 1000;
		$results[$times] = $this->recursiveVsNonrecursive($provider, $times);
		$times = 10000;
		$results[$times] = $this->recursiveVsNonrecursive($provider, $times);
		$times = 30000;
		$results[$times] = $this->recursiveVsNonrecursive($provider, $times);
		$this->report($results);
	}

	protected function recursiveVsNonrecursive($provider, $times)
	{
		$results = array();
		// warm up
		$this->runNonRecursive($provider, $times);
		// run
		$results["non-recursive"] = $this->runNonRecursive($provider, $times);
		// warm up
		$this->runRecursive($provider, $times);
		// run
		$results["recursive"] = $this->runRecursive($provider, $times);
		return $results;
	}

	protected function runNonRecursive($provider, $times) {
		$parser = new NonRecursiveParser();
		$start = microtime(true);
		$template = $this->$provider($times);
		$parser->parse($template);
		return microtime(true) - $start;
	}

	protected function runRecursive($provider, $times) {
		$parser = new RecursiveParser();
		$start = microtime(true);
		$template = $this->$provider($times);
		$parser->parse($template);
		return microtime(true) - $start;
	}

	protected function simpleProvider($limit) {
		$template = '';
		for($i = 1; $i <= $limit; $i++) {
			$template .= sprintf("%s.one.two.three.four.five = test\n", $i);
		}
		return $template;
	}

	protected function report($results)
	{
		$headerFormat = "\n%13s%13s%13s%13s\n";
		$titleFormat = "\n%12s:";
		$floatForamt = "%13.2f";
		printf($headerFormat, "Lines", "Core (ms)", "Mine (ms)", "Factor");
		foreach($results as $title => $row) {
			$recursive = 1000 * $row['recursive'];
			$nonRecursive = 1000 * $row['non-recursive'];
			$factor = $recursive/$nonRecursive;
			printf($titleFormat, $title);
			printf($floatForamt, $recursive);
			printf($floatForamt, $nonRecursive);
			printf($floatForamt, $factor);
		}
		print("\n\n");
	}

}

