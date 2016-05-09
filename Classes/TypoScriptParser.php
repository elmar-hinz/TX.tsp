<?php

namespace ElmarHinz;

class TypoScriptParser
{
	protected $steps = [];
	protected $stack = [];
	protected $tree = [];
	protected $lineCount = 0;

	const COMMENT = '/^\//';
	const EMPTY = '/^\s*$/';
	const PATH = '/^\s*([[:alnum:].]*[[:alnum:]])\s*([=<{)])\s*(.*)$/';
	const CLOSE = '/^\s*}/';

	public function parse($input)
	{
		$result = [];
		$this->stack[] =& $this->tree;
		$lines = $this->toLines($input);
		foreach($lines as $line) {
			if(preg_match(self::EMPTY, $line)) {
				$this->log('EMPTY');
			} elseif(preg_match(self::COMMENT, $line)) {
				$this->log('COMMENT');
			} elseif(preg_match(self::PATH, $line, $matches)) {
				$this->log('PATH');
				$keys = explode('.', $matches[1]);
				if($matches[2] === '=') {
					$valueKey = array_pop($keys);
					end($this->stack);
					$local =& $this->stack[key($this->stack)];
					foreach($keys as $key) {
						$key .= '.';
						if(!key_exists($key, $local)) {
							$local[$key] = array();
						}
						$local =& $local[$key];
					}
					$local[$valueKey] = trim($matches[3]);
				} elseif($matches[2] === '{') {
					end($this->stack);
					$local =& $this->stack[key($this->stack)];
					foreach($keys as $key) {
						$key .= '.';
						if(!key_exists($key, $local)) {
							$local[$key] = array();
						}
						$local =& $local[$key];
					}
					$this->stack[] =& $local;
				}
			} elseif(preg_match(self::CLOSE, $line)) {
					array_pop($this->stack);
			} else {
				$this->log('INVALID');
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

	private function log($token)
	{
		print("\n# " . $token);
	}
}

