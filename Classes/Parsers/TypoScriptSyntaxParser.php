<?php

namespace ElmarHinz\TypoScriptParser\Parsers;

// Tokens

use ElmarHinz\TypoScriptParser\Tokens\TypoScriptCommentContextToken
    as CommentContextToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptCommentToken
    as CommentToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptConditionToken
    as ConditionToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptIgnoredToken
    as IgnoredToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptKeysPostspaceToken
    as KeysPostspaceToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptKeysToken
    as KeysToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptOperatorPostspaceToken
    as OperatorPostspaceToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptOperatorToken
    as OperatorToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptPrespaceToken
    as PrespaceToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptValueContextToken
    as ValueContextToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptValueCopyToken
    as ValueCopyToken;
use ElmarHinz\TypoScriptParser\Tokens\TypoScriptValueToken
    as ValueToken;

// Exceptions

use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptBraceInExcessException
    as BraceInExcessException;
use ElmarHinz\TypoScriptParser\Exceptions\
    TypoScriptBracesMissingAtConditionException
    as ConditionBracesException;
use ElmarHinz\TypoScriptParser\Exceptions\
    TypoScriptBracesMissingAtEndOfTemplateException
    as FinalBracesException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptKeysException
    as KeysException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptOperatorException
    as OperatorException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptUnclosedCommentException
    as UnclosedCommentException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptUnclosedConditionException
    as UnclosedConditionException;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptUnclosedValueException
    as UnclosedValueException;

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
        $tt = $this->tokenTracker;
        $et = $this->exceptionTracker;
		$context = self::DEFAULT_CONTEXT;
        for($nr = 1; $nr <= count($this->inputLines); $nr++) {
            $line = $this->inputLines[$nr - 1];
            switch($context) {
            case self::DEFAULT_CONTEXT:
                if(preg_match(self::CONDITION_REGEX, $line, $matches)) {
                    list(,$prespace, $condition) = $matches;
                    $tt->push(new PrespaceToken($prespace));
                    $tt->push(new ConditionToken($condition));
                    if($braceLevel > 0) $et->push(new ConditionBracesException(
                        $nr, $braceLevel));
                    $braceLevel = 0;
                } elseif(preg_match(self::COMMENT_REGEX, $line, $matches)) {
                    list(,$prespace, $operator, $comment) = $matches;
                    $tt->push(new PrespaceToken($prespace));
                    $tt->push(new CommentToken($operator . $comment));
                } elseif(preg_match(self::COMMENT_CONTEXT_OPEN_REGEX, $line,
                    $matches)) {
                    list(,$prespace, $operator, $comment) = $matches;
                    $tt->push(new PrespaceToken($prespace));
                    $tt->push(new CommentContextToken($operator . $comment));
                    $context = self::COMMENT_CONTEXT;
                } elseif(preg_match(self::OPERATOR_REGEX, $line, $matches)) {
                    list(,$prespace ,$keys, $space2, $operator, $space3,
                        $value) = $matches;
                    $tt->push(new PrespaceToken($prespace));
                    $tt->push(new KeysToken($keys));
                    $tt->push(new KeysPostspaceToken($space2));
                    $tt->push(new OperatorToken($operator));
                    switch($operator) {
                    case self::VALUE_CONTEXT_OPEN_OPERATOR:
                        $tt->push(new IgnoredToken($space3 . $value));
                        $context = self::VALUE_CONTEXT;
                        break;
                    case self::LEVEL_OPEN_OPERATOR:
                        $braceLevel++;
                        $tt->push(new IgnoredToken($space3 . $value));
                        break;
                    case self::ASSIGN_OPERATOR:
                        $tt->push(new OperatorPostspaceToken($space3));
                        $tt->push(new ValueToken($value));
                        break;
                    case self::COPY_OPERATOR:
                        $tt->push(new OperatorPostspaceToken($space3));
                        $tt->push(new ValueCopyToken($value));
                        break;
                    case self::MODIFY_OPERATOR:
                        $tt->push(new OperatorPostspaceToken($space3));
                        $tt->push(new ValueToken($value));
                        break;
                    case self::UNSET_OPERATOR:
                        $tt->push(new OperatorPostspaceToken($space3));
                        $tt->push(new IgnoredToken($value));
                        break;
                    }
                } elseif(preg_match(self::LEVEL_CLOSE_REGEX, $line, $matches)) {
                    $braceLevel--;
                    list(,$prespace, $operator, $excess) = $matches;
                    $tt->push(new PrespaceToken($prespace));
                    $tt->push(new OperatorToken($operator));
                    $tt->push(new IgnoredToken($excess));
                    if($braceLevel < 0) {
                        $et->push(new BraceInExcessException($nr));

                        $braceLevel = 0;
                    }
                } elseif(preg_match(self::VOID_REGEX, $line)) {
                    $tt->push(new PrespaceToken($line));
                } else {
                    $this->handleInvalidLineInDefaultContext($nr, $line);
                }
                break;
            case self::COMMENT_CONTEXT:
                if(preg_match(self::COMMENT_CONTEXT_CLOSE_REGEX, $line,
                    $matches)) {
                    list(,$space1, $operator, $excess) = $matches;
                    $tt->push(new CommentContextToken($space1 . $operator));
                    $tt->push(new IgnoredToken($excess));
                    $context = self::DEFAULT_CONTEXT;
                } else {
                    $tt->push(new CommentContextToken($line));
                }
                break;
            case self::VALUE_CONTEXT:
                if(preg_match(self::VALUE_CONTEXT_CLOSE_REGEX, $line,
                    $matches)) {
                    list(,$space1, $operator, $excess) = $matches;
                    $tt->push(new PrespaceToken($space1));
                    $tt->push(new OperatorToken($operator));
                    $tt->push(new IgnoredToken($excess));
                    $context = self::DEFAULT_CONTEXT;
                } else {
                    $tt->push(new ValueContextToken($line));
                }
                break;
            }
            $tt->nextLine();
		}
        if($braceLevel > 0)
            $et->push(new FinalBracesException($braceLevel));
        if($context == self::VALUE_CONTEXT)
            $et->push(new UnclosedValueException());
		if($context == self::COMMENT_CONTEXT)
            $et->push(new UnclosedCommentException());
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
        $tt = $this->tokenTracker;
        $et = $this->exceptionTracker;
        $tt->push(new IgnoredToken($line));
        if(!preg_match(self::VALID_KEY_REGEX, $line, $matches)) {
            $et->push(new KeysException($nr));
        }
        if(!preg_match(self::VALID_OPERATOR_REGEX, $line, $matches)) {
            $et->push(new OperatorException($nr));
        }
    }

}

