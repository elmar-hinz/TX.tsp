<?php

namespace ElmarHinz\Tests;

require_once("vendor/autoload.php");

use \ElmarHinz\TypoScriptPreProcessor as PreProcessor;
use \ElmarHinz\TypoScriptParser as DirectParser;
use \ElmarHinz\JsonTypoScriptParser as JsonParser;
use \ElmarHinz\TypoScriptToHashParser as HashParser;
use \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser as CoreParser;

define("LF", "\n");
define("TAB", "\t");

class PerformanceComparismTest
{
	private $lineFormat = "%s.one.two.three.four.five = test
";

	private $nestedFormat = "
page.%s {
	one = 1
	one.two {
		three = 3
		three.four {
			five = 5
			five.six {
				seven = 7
				seven.eight {
					nine = 9
					nine.ten {
						eleven = 11
					}
				}
			}
		}
	}
}
";

	public function main()
	{
		$provider = "lineProvider";
		$runs = [];
		print("\n\n$provider: \n");

		$times = 100;
		$runs[$times] = $this->compete($times, $provider);
		$times = 1000;
		$runs[$times] = $this->compete($times, $provider);
		$results[$provider] = $runs;

		$provider = "nestedProvider";
		$runs = [];
		print("\n\n$provider: \n");

		$times = 100;
		$runs[$times] = $this->compete($times, $provider);
		$times = 1000;
		$runs[$times] = $this->compete($times, $provider);
		$results[$provider] = $runs;
		$this->report($results);
	}

	protected function compete($times, $provider)
	{
		$results = array();
		// warm up
		$this->runPreProcessor($times, $provider);
		// run
		$results["PreProcessor"] = $this->runPreProcessor($times, $provider);
		// warm up
		$this->runCoreParser($times, $provider);
		// run
		$results["Core"] = $this->runCoreParser($times, $provider);
		// warm up
		$this->runDirectParser($times, $provider);
		// run
		$results["Direct"] = $this->runDirectParser($times, $provider);
		// warm up
		$this->runHashParser($times, $provider);
		// run
		$results["Hash"] = $this->runHashParser($times, $provider);
		// warm up
		$this->runJsonParser($times, $provider);
		// run
		$results["Json"] = $this->runJsonParser($times, $provider);

		print("\n\n$times DONE. \n");
		return $results;
	}

	protected function runCoreParser($times, $provider) {
		$parser = new CoreParser();
		$template = $this->$provider($times);
		$start = microtime(true);
		$parser->parse($template);
		$result = $parser->setup;
		if ($times == 100)
			file_put_contents('/tmp/core.txt', json_encode($result));
		return microtime(true) - $start;
	}

	protected function runPreProcessor($times, $provider) {
		$parser = new PreProcessor();
		$template = $this->$provider($times);
		$start = microtime(true);
		$parser->appendTemplate($template);
		$result = $parser->parse();
		if ($times == 100)
			file_put_contents('/tmp/preprocessor.txt', json_encode($result));
		return microtime(true) - $start;
	}

	protected function runDirectParser($times, $provider) {
		$parser = new DirectParser();
		$template = $this->$provider($times);
		$start = microtime(true);
		$parser->appendTemplate($template);
		$result = $parser->parse();
		if ($times == 100)
			file_put_contents('/tmp/direct.txt', json_encode($result));
		return microtime(true) - $start;
	}

	protected function runHashParser($times, $provider) {
		$parser = new HashParser();
		$template = $this->$provider($times);
		$start = microtime(true);
		$parser->appendTemplate($template);
		$result = $parser->parse();
		if ($times == 100)
			file_put_contents('/tmp/hash.txt', json_encode($result));
		return microtime(true) - $start;
	}

	protected function runJsonParser($times, $provider) {
		$parser = new JsonParser();
		$template = $this->$provider($times);
		$start = microtime(true);
		$parser->appendTemplate($template);
		$result = $parser->parse();
		if ($times == 100)
			file_put_contents('/tmp/json.txt', json_encode($result));
		return microtime(true) - $start;
	}

	protected function report($results)
	{
		print_r($results);
	}

	protected function lineProvider($limit) {
		$template = '';
		for($i = 1; $i <= $limit; $i++)
			$template .= sprintf($this->lineFormat, $i);
		return $template;
	}

	protected function nestedProvider($limit) {
		$template = '';
		for($i = 1; $i <= $limit; $i++) {
			/* $n = rand(1, 1000); */
			$n = $i;
			$template .= sprintf($this->nestedFormat, $n);
		}
		return $template;
	}

}

