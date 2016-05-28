<?php

namespace ElmarHinz\TypoScript;

class TypoScriptParser extends AbstractTypoScriptParser
{

	protected $valueModifier;

	public function setValueModifier($modifier)
	{
		$this->valueModifier = $modifier;
	}

	/**
	 * Parse TypoScript
	 *
	 * The lines to parse are filled in before by use of the method
	 * appendTemplate().
	 *
	 * - There is a tree $tree holding the already parsed in TS.
	 * - There is a pointer $pointer pointing to the position in the tree, that
	 *   is reached by the path of the current line.
	 * - There is a stack $stack stacking pointer positions, one per level of
	 *   braces, jumping over multiple keys of the path.
	 *
	 * The purpose of the stack is, to move the pointer back to the above
	 * position in the tree, whenever a brace closes, within one single jump,
	 * despite the multiple keys.
	 *
	 * A path, that does just assign a value, isn't pushed onto the stack at
	 * all else would be popped immediatly.
	 *
	 * @return array The TypoScript tree.
	 */
	public function parse() : array
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
				$value = ltrim($value);
				if($operator !== self::OPEN)
					$valueKey = array_pop($keys);
				// Reference to the last entry. How to improve?
				end($stack);
				$pointer =& $stack[key($stack)];
				foreach($keys as $key) {
					$key .= self::DOT;
					if(!key_exists($key, $pointer)) { $pointer[$key] = array(); }
					$pointer =& $pointer[$key];
				}
				switch($operator) {
				// The operations are ordered by likelyhood, most likely first.
				case self::OPEN:
					$stack[] =& $pointer;
					break;
				case self::ASSIGN:
					$pointer[$valueKey] = $value;
					break;
				case self::COPY:
					$pair = $this->copyByPath($tree, $value);
					$pointer[$valueKey] = $pair[0];
					$pointer[$valueKey . self::DOT] = $pair[1];
					break;
				case self::MODIFY:
					$pointer[$valueKey] =
						$this->modify($pointer[$valueKey], $value);
					break;
				case self::UNSET:
					unset($pointer[$valueKey]);
					unset($pointer[$valueKey . self::DOT]);
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

	protected function modify($value, $operation)
	{
		$pattern = '/^([[:alpha:]]+)\\s*\\((.*)\\).*/';
		if(preg_match($pattern, $operation, $matches)) {
			list(,$modifier, $argument) = $matches;
			if($this->valueModifier) {
				return $this->valueModifier->modifyValue($value, $modifier, $argument);
			} else {
				return $value;
			}
		} else {
			// Error handling: not well formatted modifier
		}
	}

	/**
	 * Return the value or a deep copy of the sub array of the TS tree the path
	 * is pointing to.
	 *
	 * Path resolving in done by reference for reasons of performance.
	 * The result is returned as a copy.
	 *
	 * Path is i.e.: page.10.10
	 *
	 * @param $tree Reference to the TS tree.
	 * @param $path The path to the sub array or value.
	 */
	public function copyByPath(&$tree, $path)
	{
		$keys = explode(self::DOT, trim($path));
		$valueKey = array_pop($keys);
		$pointer =& $tree;
		foreach($keys as $key) {
			$pointer =& $pointer[$key . self::DOT];
		}
		return [$pointer[$valueKey], $pointer[$valueKey . self::DOT]];
	}

}

