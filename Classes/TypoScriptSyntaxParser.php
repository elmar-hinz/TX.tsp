<?php

namespace ElmarHinz\TypoScript;

class TypoScriptSyntaxParser extends AbstractTypoScriptParser
{
	/**
	 * Formatter strings
	 */
	const COMPOSE_FORMAT = '<pre class="ts-hl">%s</pre>';
	const ELEMENT_FORMAT = '<span class="%s">%s</span>';
	const ERROR_FORMAT =
		' <span class="ts-error"><strong> - ERROR:</strong> %s</span>';
	const LINE_FORMAT = '%s%s%s';
	const LINE_NUMBER_FORMAT = '<span class="ts-linenum">%4d: </span>';

	/**
	 * CSS classes of highligthed elements and errors
	 */
	const COMMENT_CLASS            = 'ts-comment';
	const CONDITION_CLASS          = 'ts-condition';
	const DEFAULT_CLASS            = 'ts-default';
	const IGNORED_CLASS            = 'ts-ignored';
	const KEYS_CLASS               = 'ts-objstr';
	const KEYS_POSTSPACE_CLASS     = 'ts-objstr_postspace';
	const OPERATOR_CLASS           = 'ts-operator';
	const OPERATOR_POSTSPACE_CLASS = 'ts-operator_postspace';
	const PRESPACE_CLASS           = 'ts-prespace';
	const VALUE_CLASS              = 'ts-value';
	const VALUE_COPY_CLASS         = 'ts-value_copy';

	/**
	 * Collect the elements of the current line.
	 */
	protected $elementsOfCurrentLine = [];

	/**
	 * Collect the errors of the current line.
	 */
	protected $errorsOfCurrentLine = [];

	/**
	 * Collect the lines.
	 */
	protected $lines = [];

	/**
	 * Parse the lines to check and highlight the syntax
	 *
	 * Conditions are highlighted, but not evaluated in any way,
	 * because all lines need highlighting.
	 *
	 * Brace level errors are tracked:
	 *
	 * - if a closing brace is in excess.
	 * - if not all braces are closed at a condition.
	 * - if not all braces are closed at the end of the script.
	 *
	 * In case of a brace level error, the brace level is reset to zero
	 * at that line.
	 *
	 * @return void
	 */
	public function parse()
	{
		$keys = [];
		$valueKey = self::EMPTY_STRING;
		$operator = self::EMPTY_STRING;
		$value = self::EMPTY_STRING;
		$context = self::DEFAULT_CONTEXT;
		$lineCount = count($this->inputLines);
		for($lineNumber = 0; $lineNumber < $lineCount; $lineNumber++) {
			$line = $this->inputLines[$lineNumber];
			switch($context) {
			case self::DEFAULT_CONTEXT:
				if(preg_match(self::OPERATOR_REGEX, $line, $matches)) {
					list(,$prespace ,$keys, $space2, $operator, $space3,
						$value) = $matches;
					$this->pushElement(self::PRESPACE_CLASS, $prespace);
					$this->pushElement(self::KEYS_CLASS, $keys);
					$this->pushElement(self::KEYS_POSTSPACE_CLASS, $space2);
					$this->pushElement(self::OPERATOR_CLASS, $operator);
					$this->pushElement(self::OPERATOR_POSTSPACE_CLASS, $space3);
					switch($operator) {
					case self::VALUE_CONTEXT_OPEN_OPERATOR:
						$this->pushElement(self::IGNORED_CLASS, $value);
						$context = self::VALUE_CONTEXT;
						break;
					case self::LEVEL_OPEN_OPERATOR:
						$this->pushElement(self::IGNORED_CLASS, $value);
						break;
					case self::ASSIGN_OPERATOR:
						$this->pushElement(self::VALUE_CLASS, $value);
						break;
					case self::COPY_OPERATOR:
						$this->pushElement(self::VALUE_COPY_CLASS, $value);
						break;
					case self::MODIFY_OPERATOR:
						$this->pushElement(self::VALUE_CLASS, $value);
						break;
					case self::UNSET_OPERATOR:
						$this->pushElement(self::IGNORED_CLASS, $value);
						break;
					}
				} elseif(preg_match(self::LEVEL_CLOSE_REGEX, $line,
					$matches)) {
					list(,$prespace, $operator, $excess) = $matches;
					$this->pushElement(self::PRESPACE_CLASS, $prespace);
					$this->pushElement(self::OPERATOR_CLASS, $operator);
					$this->pushElement(self::IGNORED_CLASS, $value);
				} elseif(preg_match(self::VOID_REGEX, $line, $matches)) {
					list(,$prespace) = $matches;
					$this->pushElement(self::PRESPACE_CLASS, $prespace);
				} elseif(preg_match(self::COMMENT_REGEX, $line, $matches)) {
					list(,$prespace, $operator, $comment) = $matches;
					$this->pushElement(self::PRESPACE_CLASS, $prespace);
					$this->pushElement(self::COMMENT_CLASS, $operator
						. $comment);
				} elseif(preg_match(self::CONDITION_REGEX, $line, $matches)) {
					list(,$prespace, $condition) = $matches;
					$this->pushElement(self::PRESPACE_CLASS, $prespace);
					$this->pushElement(self::CONDITION_CLASS, $condition);
					// skip
				} elseif(preg_match(self::COMMENT_CONTEXT_OPEN_REGEX, $line,
					$matches)) {
					list(,$prespace, $operator, $comment) = $matches;
					$this->pushElement(self::PRESPACE_CLASS, $prespace);
					$this->pushElement(self::COMMENT_CLASS, $operator
						. $comment);
					$context = self::COMMENT_CONTEXT;
				} else {
					$this->pushElement(self::IGNORED_CLASS, $line);
					// TODO: push error
				}
				break;
			case self::COMMENT_CONTEXT:
				if(preg_match(self::COMMENT_CONTEXT_CLOSE_REGEX, $line,
					$matches)) {
					list(,$space1, $operator, $space2, $excess) = $matches;
					$this->pushElement(self::COMMENT_CLASS, $space1.$operator);
					$this->pushElement(self::OPERATOR_POSTSPACE_CLASS, $space2);
					$this->pushElement(self::IGNORED_CLASS, $excess);
					$context = self::DEFAULT_CONTEXT;
				} else {
					$this->pushElement(self::COMMENT_CLASS, $line);
				}
				break;
			case self::VALUE_CONTEXT:
				if(preg_match(self::VALUE_CONTEXT_CLOSE_REGEX, $line,
					$matches)) {
					list(,$space1, $operator, $space2, $excess) = $matches;
					$this->pushElement(self::PRESPACE_CLASS, $space1);
					$this->pushElement(self::OPERATOR_CLASS, $operator);
					$this->pushElement(self::OPERATOR_POSTSPACE_CLASS, $space2);
					$this->pushElement(self::IGNORED_CLASS, $excess);
					$context = self::DEFAULT_CONTEXT;
				} else {
					$this->pushElement(self::VALUE_CLASS, $line);
				}
				break;
			}
			$this->finishLine($lineNumber);
		}
		return $this->finish();
	}

	protected function pushElement($class, $element)
	{
		$format = self::ELEMENT_FORMAT;
		$element = htmlspecialchars($element);
		$this->elementsOfCurrentLine[] = sprintf($format, $class, $element);
	}

	protected function pushError($message)
	{
		$this->errorsOfCurrentLine[] = $message;
	}

	protected function finishLine($lineNumber)
	{
		$elements = '';
		$errors = '';
		if($this->elementsOfCurrentLine) {
			$elements = implode('', $this->elementsOfCurrentLine);
		}
		if($this->errorsOfCurrentLine) {
			$errors = implode('; ', $this->errorsOfCurrentLine);
			$this->highligthed .= sprintf(self::ERROR_FORMAT, $errors);
		}
		$nr = sprintf(self::LINE_NUMBER_FORMAT, $lineNumber);
		$this->lines[] = sprintf(self::LINE_FORMAT, $nr, $elements, $errors);
		$this->elementsOfCurrentLine = [];
		$this->errorsOfCurrentLine = [];
	}

	protected function finish()
	{
		return sprintf(self::COMPOSE_FORMAT, implode("\n", $this->lines));
	}

}

