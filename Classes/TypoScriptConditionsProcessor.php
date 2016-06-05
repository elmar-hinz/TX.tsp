<?php

namespace ElmarHinz\TypoScriptParser;

class TypoScriptConditionsProcessor extends AbstractTypoScriptParser
{

	protected $matcher;
	protected $previousConditionFailed = false;

	public function setMatcher($matchObject)
	{
		$this->matcher = $matchObject;
	}

	public function parse()
	{
		$lines = [];
		$doCollect = true;
		foreach($this->inputLines as $line) {
			$trimmed = ltrim($line);
			if($trimmed && $trimmed[0] == '[') {
				$doCollect = $this->handleCondition($trimmed);
			} elseif ($doCollect) {
				$lines[] = $line;
			}
		}
		return $lines;
	}

	protected function handleCondition($trimmedLine) {
		$condition = $trimmedLine;
		$tag = strtoupper($condition);
		$doCollect = true;
		if ($tag == '[GLOBAL]' || $tag == '[END]') {
			$doCollect = true;
		} elseif ($tag == '[ELSE]') {
		   	if($this->previousConditionFailed) {
				$doCollect = true;
			} else {
				$doCollect = false;
			}
		} else {
			if($this->matcher->match($condition)) {
		   		$this->previousConditionFailed = false;
				$doCollect = true;
			} else {
		   		$this->previousConditionFailed = true;
				$doCollect = false;
			}
		}
		return $doCollect;
	}

}

