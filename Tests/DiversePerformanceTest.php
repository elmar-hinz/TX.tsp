<?php

namespace ElmarHinz\TypoScript\Tests;

require_once("vendor/autoload.php");

class DiversePerformanceTest {

	public function main()
	{
		$tests[] =  [ 10000, 'randomConditionProvider', 'firstCharByBracket', ];
		$tests[] =  [ 10000, 'randomConditionProvider', 'firstCharBySubstr', ];
		foreach($tests as $test) {
			$count = $test[0];
			$provider = $test[1];
			$execution = $test[2];
			$data = $this->$provider($count);
			$start = microtime(true);
			$this->$execution($data);
			$stop = microtime(true);
			printf("\n%s times %s: %f", $count, $execution, $stop - $start);
		}
	}

	public function firstCharBySubstr($data)
	{
		foreach($data as $entry) {
			$char = substr($entry, 0, 1);
		}
	}

	public function firstCharByBracket($data)
	{
		foreach($data as $entry) {
			$char = $entry[0];
		}
	}

	public function randomConditionProvider($count)
	{
		$lines = [];
		for($i = 1; $i <= $count; $i++) {
			if(rand(0, 99) < 10) {
				$lines[] = '[CONDITION]';
			} else {
				$lines[] = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
			}
		}
		return $lines;
	}

}

