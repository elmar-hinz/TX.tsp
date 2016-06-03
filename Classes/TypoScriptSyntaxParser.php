<?php

namespace ElmarHinz\TypoScript;

class TypoScriptSyntaxParser extends AbstractTypoScriptParser
{

	/**
	 * Parse the lines to check and highlight the syntax
	 *
	 * Conditions are highlighted, but not evaluated in any way, because all
	 * lines need highlighting. For the production TypoScript parser,
	 * conditions are preprocessed. To refelect this overall priority of
	 * conditions here they are processed before branching into contexts.
	 *
	 * Whenever a condition line is matched (including ELSE, END, GLOBAL), all
	 * states must be at default, otherwise they are reset and an error is
	 * reported:
	 *
	 * - if a mulitline comment was not closed
	 * - if a mulitline value was not closed
	 * - if braces were not closed.
	 *
	 * The same checks are done at the and of the stript.
	 *
	 * If a closing brace is in excess anywhere an error is reported
	 * and the brace level is set to zero.
	 *
	 * @return void
	 */
	public function parse()
	{
		$braceLevel = 0;
		$f = $this->formatter;
		$context = self::DEFAULT_CONTEXT;
		foreach($this->inputLines as $line) {
			if(preg_match(self::CONDITION_REGEX, $line, $matches)) {
				list(,$prespace, $condition) = $matches;
				$f->pushToken(self::PRESPACE_TOKEN, $prespace);
				$f->pushToken(self::CONDITION_TOKEN, $condition);
				if($braceLevel > 0) {
					$f->pushError(
						self::POSITIVE_KEYS_LEVEL_AT_CONDITION_ERROR,
						$braceLevel
					);
				}
				if($context == self::VALUE_CONTEXT) $f->pushError(
					self::UNCLOSED_VALUE_CONTEXT_AT_CONDITION_ERROR);
				if($context == self::COMMENT_CONTEXT) $f->pushError(
					self::UNCLOSED_COMMENT_CONTEXT_AT_CONDITION_ERROR);
				$braceLevel = 0;
				$context = self::DEFAULT_CONTEXT;
			}
			switch($context) {
			case self::DEFAULT_CONTEXT:
				if(preg_match(self::OPERATOR_REGEX, $line, $matches)) {
					list(,$prespace ,$keys, $space2, $operator, $space3,
						$value) = $matches;
					$f->pushToken(self::PRESPACE_TOKEN, $prespace);
					$f->pushToken(self::KEYS_TOKEN, $keys);
					$f->pushToken(self::KEYS_POSTSPACE_TOKEN, $space2);
					$f->pushToken(self::OPERATOR_TOKEN, $operator);
					switch($operator) {
					case self::VALUE_CONTEXT_OPEN_OPERATOR:
						$f->pushToken(self::IGNORED_TOKEN, $space3 . $value);
						$context = self::VALUE_CONTEXT;
						break;
					case self::LEVEL_OPEN_OPERATOR:
						$braceLevel++;
						$f->pushToken(self::IGNORED_TOKEN, $space3 . $value);
						break;
					case self::ASSIGN_OPERATOR:
						$f->pushToken(self::OPERATOR_POSTSPACE_TOKEN, $space3);
						$f->pushToken(self::VALUE_TOKEN, $value);
						break;
					case self::COPY_OPERATOR:
						$f->pushToken(self::OPERATOR_POSTSPACE_TOKEN, $space3);
						$f->pushToken(self::VALUE_COPY_TOKEN, $value);
						break;
					case self::MODIFY_OPERATOR:
						$f->pushToken(self::OPERATOR_POSTSPACE_TOKEN, $space3);
						$f->pushToken(self::VALUE_TOKEN, $value);
						break;
					case self::UNSET_OPERATOR:
						$f->pushToken(self::OPERATOR_POSTSPACE_TOKEN, $space3);
						$f->pushToken(self::IGNORED_TOKEN, $value);
						break;
					}
				} elseif(preg_match(self::LEVEL_CLOSE_REGEX, $line, $matches)) {
					$braceLevel--;
					list(,$prespace, $operator, $excess) = $matches;
					$f->pushToken(self::PRESPACE_TOKEN, $prespace);
					$f->pushToken(self::OPERATOR_TOKEN, $operator);
					$f->pushToken(self::IGNORED_TOKEN, $excess);
					if($braceLevel < 0) {
						$f->pushError(self::NEGATIVE_KEYS_LEVEL_ERRROR);
						$braceLevel = 0;
					}
				} elseif(preg_match(self::VOID_REGEX, $line)) {
					$f->pushToken(self::PRESPACE_TOKEN, $line);
				} elseif(preg_match(self::COMMENT_REGEX, $line, $matches)) {
					list(,$prespace, $operator, $comment) = $matches;
					$f->pushToken(self::PRESPACE_TOKEN, $prespace);
					$f->pushToken(self::COMMENT_TOKEN, $operator . $comment);
				} elseif(preg_match(self::COMMENT_CONTEXT_OPEN_REGEX, $line,
					$matches)) {
					list(,$prespace, $operator, $comment) = $matches;
					$f->pushToken(self::PRESPACE_TOKEN, $prespace);
					$f->pushToken(self::COMMENT_CONTEXT_TOKEN, $operator
						. $comment);
					$context = self::COMMENT_CONTEXT;
				} else {
					$f->pushToken(self::IGNORED_TOKEN, $line);
					// TODO: push error
				}
				break;
			case self::COMMENT_CONTEXT:
				if(preg_match(self::COMMENT_CONTEXT_CLOSE_REGEX, $line,
					$matches)) {
					list(,$space1, $operator, $excess) = $matches;
					$f->pushToken(self::COMMENT_CONTEXT_TOKEN,
						$space1.$operator);
					$f->pushToken(self::IGNORED_TOKEN, $excess);
					$context = self::DEFAULT_CONTEXT;
				} else {
					$f->pushToken(self::COMMENT_CONTEXT_TOKEN, $line);
				}
				break;
			case self::VALUE_CONTEXT:
				if(preg_match(self::VALUE_CONTEXT_CLOSE_REGEX, $line,
					$matches)) {
					list(,$space1, $operator, $excess) = $matches;
					$f->pushToken(self::PRESPACE_TOKEN, $space1);
					$f->pushToken(self::OPERATOR_TOKEN, $operator);
					$f->pushToken(self::IGNORED_TOKEN, $excess);
					$context = self::DEFAULT_CONTEXT;
				} else {
					$f->pushToken(self::VALUE_CONTEXT_TOKEN, $line);
				}
				break;
			}
			$f->finishLine();
		}
		if($braceLevel > 0)
			$f->pushError(self::POSITIVE_KEYS_LEVEL_AT_END_ERROR, $braceLevel);
		if($context == self::VALUE_CONTEXT)
			$f->pushError(self::UNCLOSED_VALUE_CONTEXT_AT_END_ERROR);
		if($context == self::COMMENT_CONTEXT)
			$f->pushError(self::UNCLOSED_COMMENT_CONTEXT_AT_END_ERROR);
		return $f->finish();
	}

}

