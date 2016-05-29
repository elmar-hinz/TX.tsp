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
	 * despite the multiple keys. A path, that does just assign a value, isn't
	 * pushed onto the stack at all else would be popped immediatly.
	 *
	 * This function is optimised to be fast. It's by intention, that no
	 * recursion is done. Calling functions is more expensive then simple
	 * loops. Apart from the rarely occuring copy and modifier methods no
	 * methods are called. The price are a more lines than in code optimised
	 * for readability. Switch-case and if are ordered by likelyhood, most
	 * likely first.
	 *
	 * @return array The TypoScript tree.
	 */
	public function parse() : array
	{
		$tree = [];
		$stack[] =& $tree;
		$pointer = null;
		$keys = [];
		$valueKey = self::EMPTY_STRING;
		$operator = self::EMPTY_STRING;
		$value = self::EMPTY_STRING;
		$context = self::OUTER_CONTEXT;
		for($nr = 0; $nr < count($this->inputLines); $nr++) {
			$line = $this->inputLines[$nr];
			switch($context) {
			case self::OUTER_CONTEXT:
				if(preg_match(self::PATH, $line, $matches)) {
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
						if(!key_exists($key, $pointer))
							$pointer[$key] = array();
						$pointer =& $pointer[$key];
					}
					switch($operator) {
					case self::OPEN:
						$stack[] =& $pointer;
						break;
					case self::ASSIGN:
						$pointer[$valueKey] = $value;
						break;
					case self::COPY:
						$pair = $this->copyByPath($tree, $pointer, $value);
						$pointer[$valueKey] = $pair[0];
						$pointer[$valueKey . self::DOT] = $pair[1];
						break;
					case self::MULITLINE_VALUE_OPEN:
						$value = self::EMPTY_STRING;
						$context = self::MULITLINE_VALUE_CONTEXT;
						break;
					case self::MODIFY:
						if($this->valueModifier) {
							$pointer[$valueKey] = $this->valueModifier
								->modifyValue($pointer[$valueKey], $value);
						}
						break;
					case self::UNSET:
						unset($pointer[$valueKey]);
						unset($pointer[$valueKey . self::DOT]);
						break;
					}
				} elseif(preg_match(self::CLOSE, $line)) {
					array_pop($stack);
				} elseif(preg_match(self::VOID, $line)) {
					// skip
				} elseif(preg_match(self::COMMENT, $line)) {
					// skip
				} elseif(preg_match(self::MULTILINE_COMMENT_OPEN, $line)) {
					$context = self::MULITLINE_COMMENT_CONTEXT;
				} else {
					// Give error feedback here.
					print("ERROR, last operator: $operator \n");
				}
				break;
			case self::MULITLINE_COMMENT_CONTEXT:
				if(preg_match(self::MULTILINE_COMMENT_CLOSE, $line)) {
					$context = self::OUTER_CONTEXT;
				}
				break;
			case self::MULITLINE_VALUE_CONTEXT:
				if(preg_match(self::MULTILINE_VALUE_CLOSE, $line)) {
					$pointer[$valueKey] =
						$value === self::EMPTY_STRING ? $value :
						substr($value, 0, -1);
					$context = self::OUTER_CONTEXT;
				} else {
					$value .= $line . self::NL;
				}
				break;
			}
		}
		return $tree;
	}

	/**
	 * Extract subtree by given path
	 *
	 * Absolute path like:  page.10.10
	 * Relative path like:  .10.10
	 *
	 * I the path is absolute $tree will be used.
	 * I the path is relative $context will be used.
	 *
	 * Path resolving in done by reference for reasons of performance.
	 * The result is returned as a copy.
	 *
	 * @param $tree The TS tree.
	 * @param $path The path to the sub array or value.
	 */
	public function copyByPath(&$tree, &$context, $path)
	{
		$path = trim($path);
		if($path[0] === self::DOT) {
			$path = substr($path, 1);
			$pointer =& $context;
		} else {
			$pointer =& $tree;
		}
		$keys = explode(self::DOT, $path);
		$valueKey = array_pop($keys);
		foreach($keys as $key)
			$pointer =& $pointer[$key . self::DOT];
		return [$pointer[$valueKey], $pointer[$valueKey . self::DOT]];
	}

}

