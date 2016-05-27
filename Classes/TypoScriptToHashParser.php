<?php

namespace ElmarHinz\TypoScript;

class TypoScriptToHashParser extends AbstractTypoScriptParser
{

	public function parse()
	{
		$hash = [];
		$stack = [];
		for($i = 0; $i < count($this->inputLines); $i++) {
			$line = $this->inputLines[$i];
			if(preg_match(self::VOID, $line)) {
				// skip
			} elseif(preg_match(self::COMMENT, $line)) {
				// skip
			} elseif(preg_match(self::PATH, $line, $matches)) {
				list(,$path, $operator, $value) = $matches;
				if (count($stack) > 0) $path = '.'.$path;
				if($operator === '{') {
					array_push($stack, $path);
				} elseif($operator === '=') {
					$fullPath = implode("",$stack) . $path;
					$hash[$fullPath] = trim($value);
				} else {
					throw new \Execption("Unexpected char in line: ".$i.".");
				}
			} elseif(preg_match(self::CLOSE, $line)) {
					array_pop($stack);
			} else {
				// Give error feedback here.
			}
		}
		return $hash;
	}

}

