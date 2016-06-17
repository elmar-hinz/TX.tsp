<?php

namespace ElmarHinz\TypoScriptParser\Parsers;

class TypoScriptConditionsPreProcessor extends AbstractTypoScriptParser
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
		$context = self::DEFAULT_CONTEXT;
		foreach($this->inputLines as $line) {
			switch($context) {
			case self::DEFAULT_CONTEXT:
				if(preg_match(self::CONDITION_REGEX, $line)) {
                    $doCollect = $this->handleCondition($line);
                } elseif(preg_match(self::COMMENT_CONTEXT_OPEN_REGEX, $line)) {
					$context = self::COMMENT_CONTEXT;
                    if($doCollect) $lines[] = $line;
                } elseif(preg_match(self::VALUE_CONTEXT_OPEN_REGEX, $line)) {
					$context = self::VALUE_CONTEXT;
                    if($doCollect) $lines[] = $line;
                } else {
                    if($doCollect) $lines[] = $line;
                }
                break;
			case self::COMMENT_CONTEXT:
				if(preg_match(self::COMMENT_CONTEXT_CLOSE_REGEX, $line)) {
					$context = self::DEFAULT_CONTEXT;
				}
                if($doCollect) $lines[] = $line;
                break;
			case self::VALUE_CONTEXT:
				if(preg_match(self::VALUE_CONTEXT_CLOSE_REGEX, $line)) {
					$context = self::DEFAULT_CONTEXT;
                }
                if($doCollect) $lines[] = $line;
                break;
            }
        }
		return $lines;
	}

	protected function handleCondition($line) {
		$condition = trim($line);
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

