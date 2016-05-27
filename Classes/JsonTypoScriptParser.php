<?php

namespace ElmarHinz\TypoScript;

use \ElmarHinz\TypoScript\TypoScriptToHashParser as HashParser;

class JsonTypoScriptParser extends AbstractTypoScriptParser
{

	public function parse()
	{
		$oldKeys = [];
		$json = '{ '; // Space need to be stripped for empty set
		// The hash Parser preprosesses to do the overrides
		// and returns a fast non-nesting array.
		$hashParser = new HashParser();
		$hashParser->appendTemplate($this->inputLines);
		$entries = $hashParser->parse();
		ksort($entries, SORT_NATURAL);
		$matches = [];
		$pattern = "/[[:alnum:]]+\.?/";
		foreach( $entries as $path => $value) {
			preg_match_all($pattern, $path, $matches,PREG_PATTERN_ORDER);
			$valueKey = array_pop($matches[0]);
			// reverse for performance
			// to use pop instead of unshift
			$newKeys = array_reverse($matches[0]);
			$consume = $newKeys;
			$commonPath = true;
			// find the common part of path
			while($commonPath) {
				if (!empty($consume) && !empty($oldKeys)
					&& (end($consume) === end($oldKeys))) {
						array_pop($consume);
						array_pop($oldKeys);
				} else {
					$commonPath = false;
				}
			}
			// close for the rest
			for($i = 0; $i < count($oldKeys); $i++) {
				$json = substr($json, 0, -1);
				$json .= '},';
			}
			// open new
			for($i = count($consume) - 1; $i >= 0; $i--) {
				$json .= '"'.$consume[$i].'" : {';
			}
			// set value
			$json .= '"'.$valueKey.'" : "'.$value.'",';
			$oldKeys = $newKeys;
		}
		// close the rest
		$json = substr($json, 0, -1);
		for($i = 0; $i < count($oldKeys); $i++) $json .= '}';
		// mirror the first
		$json .= '}';
		return json_decode($json, true);
	}

}

