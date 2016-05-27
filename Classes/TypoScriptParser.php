<?php

namespace ElmarHinz;

class TypoScriptParser extends AbstractTypoScriptParser
{

	public function parse()
	{
		$tree = [];
		$stack[] =& $tree;
		foreach($this->inputLines as $line) {
			if(preg_match(self::VOID, $line)) {
				// skip
			} elseif(preg_match(self::COMMENT, $line)) {
				// skip
			} elseif(preg_match(self::PATH, $line, $matches)) {
				$keys = explode('.', $matches[1]);
				if($matches[2] === '=') $valueKey = array_pop($keys);
				// Reference to the last entry. How to improve?
				end($stack);
				$pointer =& $stack[key($stack)];
				foreach($keys as $key) {
					$key .= '.';
					if(!key_exists($key, $pointer)) {
						$pointer[$key] = array();
					}
					$pointer =& $pointer[$key];
				}
				if($matches[2] === '=') {
					$pointer[$valueKey] = trim($matches[3]);
				} elseif($matches[2] === '{') {
					$stack[] =& $pointer;
				}
			} elseif(preg_match(self::CLOSE, $line)) {
					array_pop($stack);
			} else {
				// Give error feedback here.
			}
		}
		return $tree;
	}

}

