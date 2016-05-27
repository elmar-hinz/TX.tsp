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
				list(,$keys, $operator, $value) = $matches;
				$keys = explode(self::DOT, $keys);
				if($operator === self::ASSIGN || $operator === self::UNSET)
					$valueKey = array_pop($keys);
				// Reference to the last entry. How to improve?
				end($stack);
				$pointer =& $stack[key($stack)];
				foreach($keys as $key) {
					$key .= self::DOT;
					if(!key_exists($key, $pointer)) {
						$pointer[$key] = array();
					}
					$pointer =& $pointer[$key];
				}
				switch($operator) {
				case self::ASSIGN:
					$pointer[$valueKey] = ltrim($value);
					break;
				case self::OPEN:
					$stack[] =& $pointer;
					break;
				case self::COPY:
					// TODO
					break;
				case self::UNSET:
					unset($pointer[$valueKey]);
					unset($pointer[$valueKey . self::DOT]);
					break;
				case self::ALTER:
					// TODO
					break;
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

