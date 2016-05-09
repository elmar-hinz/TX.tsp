<?php

namespace ElmarHinz;

class TypoScriptParser
{
	protected $lineCount = 0;
	protected $stack = [];
	protected $tree = [];

	const COMMENT = '/^\//';
	const VOID = '/^\s*$/';
	const PATH = '/^\s*([[:alnum:].]*[[:alnum:]])\s*([=<{)])\s*(.*)$/';
	const CLOSE = '/^\s*}/';

	/*
	 * Tree to be extended by parsing additional TS templates
	 *
	 * Preset a an array with a TS tree that will be
	 * extended by overruling it with every call to parse().
	 *
	 * The array has the same data model as the expected result
	 * of parse().
	 *
	 * @param array	The tree
	 * @return void
	 */
	public function presetTree($treeArray)
	{
		$this->tree = $treeArray;
	}

	public function parse($input)
	{
		$result = [];
		$this->stack[] =& $this->tree;
		$lines = $this->toLines($input);
		foreach($lines as $line) {
			if(preg_match(self::VOID, $line)) {
				// skip
			} elseif(preg_match(self::COMMENT, $line)) {
				// skip
			} elseif(preg_match(self::PATH, $line, $matches)) {
				$keys = explode('.', $matches[1]);
				if($matches[2] === '=') $valueKey = array_pop($keys);
				// Reference to the last entry. How to improve?
				end($this->stack);
				$pointer =& $this->stack[key($this->stack)];
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
					$this->stack[] =& $pointer;
				}
			} elseif(preg_match(self::CLOSE, $line)) {
					array_pop($this->stack);
			} else {
				// Give error feedback here.
			}
		}
		return $this->tree;
	}

	protected function toLines($input)
	{
		$lines = [];
		if(!is_array($input))
		{
			$lines = explode('\n', $input);
		} else {
			$lines = $input;
		}
		$this->lineCount = count($lines);
		return $lines;
	}

}

