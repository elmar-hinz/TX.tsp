<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Parsers;

// Pasrser and trackers

use ElmarHinz\TypoScriptParser\Parsers\TypoScriptSyntaxParser
    as Parser;
use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptTokenTrackerPushInterface
    as TokenTracker;
use ElmarHinz\TypoScriptParser\Interfaces\
    TypoScriptParsetimeExceptionTrackerPushInterface
    as ExceptionTracker;

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

class TypoScriptSyntaxParserTest extends \PHPUnit_Framework_TestCase
{

	public function setup()
	{
		$this->parser = new Parser();
        $this->tokenTracker  = $this->getMock(TokenTracker::class);
        $this->parser->injectTokenTracker($this->tokenTracker);
        $this->exceptionTracker  = $this->getMock(ExceptionTracker::class);
        $this->parser->injectExceptionTracker($this->exceptionTracker);
	}

	/**
	 * @test
	 */
	public function emptyString()
	{
		$typoScript = ''; // matched by VOID_REGEX
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(1))->method('push')
            ->withConsecutive(
                [new PrespaceToken('')]
            );
        $this->tokenTracker->expects($this->exactly(1))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function condition()
	{
		$typoScript = ' [CONDITION]';
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(2))->method('push')
            ->withConsecutive(
                [new PrespaceToken(' ')],
                [new ConditionToken('[CONDITION]')]
            );
        $this->tokenTracker->expects($this->exactly(1))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function comment()
	{
		$typoScript = ' # comment';
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(2))->method('push')
            ->withConsecutive(
                [new PrespaceToken(' ')],
                [new CommentToken('# comment')]
            );
        $this->tokenTracker->expects($this->exactly(1))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function assignmentWithoutPrespace()
	{
		$typoScript = 'one = 1 ';
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(6))->method('push')
            ->withConsecutive(
                [new PrespaceToken('')],
                [new KeysToken('one')],
                [new KeysPostspaceToken(' ')],
                [new OperatorToken('=')],
                [new OperatorPostspaceToken(' ')],
                [new ValueToken('1 ')]
            );
        $this->tokenTracker->expects($this->exactly(1))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function assignment()
	{
		$typoScript = ' one = 1 ';
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(6))->method('push')
            ->withConsecutive(
                [new PrespaceToken(' ')],
                [new KeysToken('one')],
                [new KeysPostspaceToken(' ')],
                [new OperatorToken('=')],
                [new OperatorPostspaceToken(' ')],
                [new ValueToken('1 ')]
            );
        $this->tokenTracker->expects($this->exactly(1))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function neseted_assignment()
	{
		$typoScript = [
			' one.two { ',
			'     three = 3 ',
			' } ',
		];
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(14))->method('push')
            ->withConsecutive(
                [new PrespaceToken(' ')],
                [new KeysToken('one.two')],
                [new KeysPostspaceToken(' ')],
                [new OperatorToken('{')],
                [new IgnoredToken(' ')],
                [new PrespaceToken('     ')],
                [new KeysToken('three')],
                [new KeysPostspaceToken(' ')],
                [new OperatorToken('=')],
                [new OperatorPostspaceToken(' ')],
                [new ValueToken('3 ')],
                [new PrespaceToken(' ')],
                [new OperatorToken('}')],
                [new IgnoredToken(' ')]
            );
        $this->tokenTracker->expects($this->exactly(3))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function modify()
	{
		$typoScript = 'one := append(xxx)  ';
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(6))->method('push')
            ->withConsecutive(
                [new PrespaceToken('')],
                [new KeysToken('one')],
                [new KeysPostspaceToken(' ')],
                [new OperatorToken(':=')],
                [new OperatorPostspaceToken(' ')],
                [new ValueToken('append(xxx)  ')]
            );
        $this->tokenTracker->expects($this->exactly(1))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function copy()
	{
		$typoScript = 'one < .two ';
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(6))->method('push')
            ->withConsecutive(
                [new PrespaceToken('')],
                [new KeysToken('one')],
                [new KeysPostspaceToken(' ')],
                [new OperatorToken('<')],
                [new OperatorPostspaceToken(' ')],
                [new ValueCopyToken('.two ')]
            );
        $this->tokenTracker->expects($this->exactly(1))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function delete()
	{
		$typoScript = ' one > excess ';
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(6))->method('push')
            ->withConsecutive(
                [new PrespaceToken(' ')],
                [new KeysToken('one')],
                [new KeysPostspaceToken(' ')],
                [new OperatorToken('>')],
                [new OperatorPostspaceToken(' ')],
                [new IgnoredToken('excess ')]
            );
        $this->tokenTracker->expects($this->exactly(1))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function value_context()
	{
		$typoScript = [
			' key ( excess1 ',
			'     content line  ',
			' ) excess2 ',
		];
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(9))->method('push')
            ->withConsecutive(
                [new PrespaceToken(' ')],
                [new KeysToken('key')],
                [new KeysPostspaceToken(' ')],
                [new OperatorToken('(')],
                [new IgnoredToken(' excess1 ')],
                [new ValueContextToken( '     content line  ')],
                [new PrespaceToken(' ')],
                [new OperatorToken(')')],
                [new IgnoredToken(' excess2 ')]
            );
        $this->tokenTracker->expects($this->exactly(3))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function comment_context()
	{
        $typoScript = [
            ' /* opening ',
            '     content line  ',
            ' */ excess2 ',
		];
		$this->parser->appendTemplate($typoScript);
        $this->tokenTracker->expects($this->exactly(5))->method('push')
            ->withConsecutive(
                [new PrespaceToken(' ')],
                [new CommentContextToken('/* opening ')],
                [new CommentContextToken('     content line  ' )],
                [new CommentContextToken(' */')],
                [new IgnoredToken(' excess2 ')]
            );
        $this->tokenTracker->expects($this->exactly(3))->method('nextLine');
        $this->exceptionTracker->expects($this->exactly(0))->method('push');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function bracesInExesss()
	{
		$typoScript = [
			' one.two { ',
			'         } ',
			'     } ',
			' } ',
		];
		$this->parser->appendTemplate($typoScript);
        $this->exceptionTracker->expects($this->exactly(2))->method('push')
            ->withConsecutive(
                [new BraceInExcessException(3)],
                [new BraceInExcessException(4)]
            );
        $this->tokenTracker->expects($this->exactly(4))->method('nextLine');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function finalBracesMissing()
	{
		$typoScript = [
			' one.two { ',
			'   three.four { ',
			'     five.six { ',
			'         } ',
		];
		$this->parser->appendTemplate($typoScript);
        $this->exceptionTracker->expects($this->exactly(1))->method('push')
            ->withConsecutive(
                [new FinalBracesException(2)]
            );
        $this->tokenTracker->expects($this->exactly(4))->method('nextLine');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function bracesMissingAtCondition()
	{
		$typoScript = [
			' one.two { ',
			'   three.four { ',
			'     five.six { ',
			'         } ',
			' [ELSE] ',
		];
		$this->parser->appendTemplate($typoScript);
        $this->exceptionTracker->expects($this->exactly(1))->method('push')
            ->withConsecutive(
                [new ConditionBracesException(5, 2)]
            );
        $this->tokenTracker->expects($this->exactly(5))->method('nextLine');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function unclosedValueContext()
	{
		$typoScript = [
			' one ( ',
			'   content',
		];
		$this->parser->appendTemplate($typoScript);
        $this->exceptionTracker->expects($this->exactly(1))->method('push')
            ->withConsecutive(
                [new UnclosedValueException()]
            );
        $this->tokenTracker->expects($this->exactly(2))->method('nextLine');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function unclosedCommentContext()
	{
		$typoScript = [
			' /* content ',
			'   content',
		];
		$this->parser->appendTemplate($typoScript);
        $this->exceptionTracker->expects($this->exactly(1))->method('push')
            ->withConsecutive(
                [new UnclosedCommentException()]
            );
        $this->tokenTracker->expects($this->exactly(2))->method('nextLine');
		$this->parser->parse();
	}

}

