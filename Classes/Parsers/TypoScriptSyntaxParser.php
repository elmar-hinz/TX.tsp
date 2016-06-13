<?php

namespace ElmarHinz\TypoScriptParser\Parsers;

use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptBraceInExcessException as BraceInExcessException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptKeysException as KeysException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptUnclosedCommentException as UnclosedCommentException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptBracesMissingAtConditionException
    as BracesMissingAtConditionException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptOperatorException as OperatorException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptUnclosedConditionException
    as UnclosedConditionException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptBracesMissingAtEndOfTemplateException
    as BracesMissingAtEndOfTemplateException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException as ParsetimeException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptUnclosedValueException as UnclosedValueException;

class TypoScriptSyntaxParser extends AbstractTypoScriptSyntaxParser
{

	/**
	 * Parse the lines to check and highlight the syntax
	 *
	 * Conditions are highlighted, but not evaluated in any way, because all
     * lines need highlighting.
	 *
     * Whenever a condition line is matched (including ELSE, END, GLOBAL),
     * the brace level must be zero, else an error is reported and the
     * brace leven is set to zero.
	 *
	 * At the end of the script following checks are done:
     *
     * - unclosed multiline comment
     * - unclosed multiline value
     * - unclosed braces
     *
     * An error is reported, when a line within the default context doesn't
     * match any of the expected patterns.
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
        for($nr = 1; $nr <= count($this->inputLines); $nr++) {
            $line = $this->inputLines[$nr - 1];
            switch($context) {
            case self::DEFAULT_CONTEXT:
                if(preg_match(self::CONDITION_REGEX, $line, $matches)) {
                    list(,$prespace, $condition) = $matches;
                    $f->pushToken($nr, self::PRESPACE_TOKEN, $prespace);
                    $f->pushToken($nr, self::CONDITION_TOKEN, $condition);
                    if($braceLevel > 0) {
                        $f->pushError($nr,
                            self::POSITIVE_KEYS_LEVEL_AT_CONDITION_ERROR,
                            $braceLevel
                        );
                    }
                    $braceLevel = 0;
                } elseif(preg_match(self::COMMENT_REGEX, $line, $matches)) {
                    list(,$prespace, $operator, $comment) = $matches;
                    $f->pushToken($nr, self::PRESPACE_TOKEN, $prespace);
                    $f->pushToken($nr, self::COMMENT_TOKEN, $operator . $comment);
                } elseif(preg_match(self::COMMENT_CONTEXT_OPEN_REGEX, $line,
                    $matches)) {
                    list(,$prespace, $operator, $comment) = $matches;
                    $f->pushToken($nr, self::PRESPACE_TOKEN, $prespace);
                    $f->pushToken($nr, self::COMMENT_CONTEXT_TOKEN, $operator
                        . $comment);
                    $context = self::COMMENT_CONTEXT;
                } elseif(preg_match(self::OPERATOR_REGEX, $line, $matches)) {
                    list(,$prespace ,$keys, $space2, $operator, $space3,
                        $value) = $matches;
                    $f->pushToken($nr, self::PRESPACE_TOKEN, $prespace);
                    $f->pushToken($nr, self::KEYS_TOKEN, $keys);
                    $f->pushToken($nr, self::KEYS_POSTSPACE_TOKEN, $space2);
                    $f->pushToken($nr, self::OPERATOR_TOKEN, $operator);
                    switch($operator) {
                    case self::VALUE_CONTEXT_OPEN_OPERATOR:
                        $f->pushToken($nr, self::IGNORED_TOKEN, $space3 . $value);
                        $context = self::VALUE_CONTEXT;
                        break;
                    case self::LEVEL_OPEN_OPERATOR:
                        $braceLevel++;
                        $f->pushToken($nr, self::IGNORED_TOKEN, $space3 . $value);
                        break;
                    case self::ASSIGN_OPERATOR:
                        $f->pushToken($nr, self::OPERATOR_POSTSPACE_TOKEN, $space3);
                        $f->pushToken($nr, self::VALUE_TOKEN, $value);
                        break;
                    case self::COPY_OPERATOR:
                        $f->pushToken($nr, self::OPERATOR_POSTSPACE_TOKEN, $space3);
                        $f->pushToken($nr, self::VALUE_COPY_TOKEN, $value);
                        break;
                    case self::MODIFY_OPERATOR:
                        $f->pushToken($nr, self::OPERATOR_POSTSPACE_TOKEN, $space3);
                        $f->pushToken($nr, self::VALUE_TOKEN, $value);
                        break;
                    case self::UNSET_OPERATOR:
                        $f->pushToken($nr, self::OPERATOR_POSTSPACE_TOKEN, $space3);
                        $f->pushToken($nr, self::IGNORED_TOKEN, $value);
                        break;
                    }
                } elseif(preg_match(self::LEVEL_CLOSE_REGEX, $line, $matches)) {
                    $braceLevel--;
                    list(,$prespace, $operator, $excess) = $matches;
                    $f->pushToken($nr, self::PRESPACE_TOKEN, $prespace);
                    $f->pushToken($nr, self::OPERATOR_TOKEN, $operator);
                    $f->pushToken($nr, self::IGNORED_TOKEN, $excess);
                    if($braceLevel < 0) {
                        $f->pushError($nr, self::NEGATIVE_KEYS_LEVEL_ERROR);
                        $braceLevel = 0;
                    }
                } elseif(preg_match(self::VOID_REGEX, $line)) {
                    $f->pushToken($nr, self::PRESPACE_TOKEN, $line);
                } else {
                    $this->handleInvalidLineInDefaultContext($nr, $line);
                }
                break;
            case self::COMMENT_CONTEXT:
                if(preg_match(self::COMMENT_CONTEXT_CLOSE_REGEX, $line,
                    $matches)) {
                    list(,$space1, $operator, $excess) = $matches;
                    $f->pushToken($nr, self::COMMENT_CONTEXT_TOKEN,
                        $space1.$operator);
                    $f->pushToken($nr, self::IGNORED_TOKEN, $excess);
                    $context = self::DEFAULT_CONTEXT;
                } else {
                    $f->pushToken($nr, self::COMMENT_CONTEXT_TOKEN, $line);
                }
                break;
            case self::VALUE_CONTEXT:
                if(preg_match(self::VALUE_CONTEXT_CLOSE_REGEX, $line,
                    $matches)) {
                    list(,$space1, $operator, $excess) = $matches;
                    $f->pushToken($nr, self::PRESPACE_TOKEN, $space1);
                    $f->pushToken($nr, self::OPERATOR_TOKEN, $operator);
                    $f->pushToken($nr, self::IGNORED_TOKEN, $excess);
                    $context = self::DEFAULT_CONTEXT;
                } else {
                    $f->pushToken($nr, self::VALUE_CONTEXT_TOKEN, $line);
                }
                break;
            }
			$f->finishLine($nr);
		}
		if($braceLevel > 0)
			$f->pushFinalError(self::POSITIVE_KEYS_LEVEL_AT_END_ERROR, $braceLevel);
		if($context == self::VALUE_CONTEXT)
			$f->pushFinalError(self::UNCLOSED_VALUE_CONTEXT_ERROR);
		if($context == self::COMMENT_CONTEXT)
			$f->pushFinalError(self::UNCLOSED_COMMENT_CONTEXT_ERROR);
		return $f->finish();
	}

    /**
     * Handle invalid line.
     *
     * The function is to be called as fallback in the
     * default context. It is assumed that the user was trying
     * to enter some keys/operator line and checks for both.
     */
    protected function handleInvalidLineInDefaultContext($nr, $line)
    {
		$f = $this->formatter;
        $f->pushToken($nr, self::IGNORED_TOKEN, $line);
        if(!preg_match(self::VALID_KEY_REGEX, $line, $matches)) {
            $f->pushError($nr, self::VALID_KEY_MISSING_ERROR);
        }
        if(!preg_match(self::VALID_OPERATOR_REGEX, $line, $matches)) {
            $f->pushError($nr, self::VALID_OPERATOR_MISSING_ERROR);
        }
    }

}

