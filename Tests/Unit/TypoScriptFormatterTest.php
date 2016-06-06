<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit;

use \ElmarHinz\TypoScriptParser\TypoScriptFormatter as Formatter;
use \ElmarHinz\TypoScriptParser\AbstractTypoScriptParser as AP;

class TypoScriptFormatterTest extends \PHPUnit_Framework_TestCase
{

	public function setup()
	{
		$this->subject = new Formatter();
	}

	/**
	 * @test
	 */
	public function countLinesFromOne()
	{
		$this->assertSame(0, $this->subject->getNumberOfLastLine());
		$this->assertSame(0, $this->subject->getCountOfLines());
		$this->subject->finishLine();
		$this->subject->finishLine();
		$this->assertSame(2, $this->subject->getNumberOfLastLine());
		$this->assertSame(2, $this->subject->getCountOfLines());
	}

	/**
	 * @test
	 */
	public function countLinesFromThousend()
	{
		$this->subject->setNumberOfFirstLine(1000);
		$this->assertSame(999, $this->subject->getNumberOfLastLine());
		$this->assertSame(0, $this->subject->getCountOfLines());
		$this->subject->finishLine();
		$this->subject->finishLine();
		$this->assertSame(1001, $this->subject->getNumberOfLastLine());
		$this->assertSame(2, $this->subject->getCountOfLines());
	}

	/**
	 * @test
	 */
	public function finishEmptyDocument()
	{
		$expect = '<pre class="ts-hl"></pre>';
		$this->assertSame($expect, $this->subject->finish());
	}

	/**
	 * @test
	 */
	public function OneEmptyLine()
	{
		$expect = '<pre class="ts-hl">'
			. '<span class="ts-linenum">   1:</span> </pre>';
		$this->subject->finishLine();
		$this->assertContains($expect, $this->subject->finish());
	}

	/**
	 * @test
	 */
	public function TwoLines()
	{
		$expect = '<span class="ts-linenum">   1:</span> '
			. "\n" .  '<span class="ts-linenum">   2:</span> ';
		$this->subject->finishLine();
		$this->subject->finishLine();
		$this->assertContains($expect, $this->subject->finish());
	}

	/**
	 * @test
	 */
	public function LineNumberOfThreeDigets()
	{
		$expect = '<span class="ts-linenum"> 111:</span> ';
		$this->subject->setNumberOfFirstLine(111);
		$this->subject->finishLine();
		$this->assertContains($expect, $this->subject->finish());
	}

	/**
	 * @test
	 */
	public function oneError()
	{
		$expect = '<span class="ts-error">'
			. '<strong> - ERROR:</strong> A closing brace in excess.</span>';
		$this->subject->pushError(AP::NEGATIVE_KEYS_LEVEL_ERROR);
		$this->subject->finishLine();
		$this->assertContains($expect, $this->subject->finish());
	}

	/**
	 * @test
	 */
	public function twoErrors()
	{
		$expect = 'A closing brace in excess. A closing brace in excess.';
		$this->subject->pushError(AP::NEGATIVE_KEYS_LEVEL_ERROR);
		$this->subject->pushError(AP::NEGATIVE_KEYS_LEVEL_ERROR);
		$this->subject->finishLine();
		$this->assertContains($expect, $this->subject->finish());
	}

	/**
	 * @test
	 * @expectedException OutOfBoundsException
	 */
	public function pushError_arguments_outOfBounds()
	{
		$this->subject->pushError(1,2,3,4);
	}

	/**
	 * @test
	 */
	public function finalErrors()
	{
		$expect1 = 'FINAL ERROR';
		$expect2 = '. ';
		$expect3 = '3';
		$this->subject->finishLine();
		$this->subject->pushError(AP::UNCLOSED_COMMENT_CONTEXT_ERROR);
		$this->subject->pushError(AP::POSITIVE_KEYS_LEVEL_AT_END_ERROR, 3);
		$result = $this->subject->finish();
		$this->assertContains($expect1, $result);
		$this->assertContains($expect2, $result);
		$this->assertContains($expect3, $result);
	}

	/**
	 * @test
	 */
	public function oneToken()
	{
		$expect = '<span class="ts-comment">xxx</span></pre>';
		$this->subject->pushToken(AP::COMMENT_TOKEN, 'xxx');
		$this->subject->finishLine();
		$this->assertContains($expect, $this->subject->finish());
	}

	/**
	 * @test
	 */
	public function twoTokens()
	{
		$expect = '<span class="ts-comment">aa</span>'
			. '<span class="ts-comment">bb</span>';
		$this->subject->pushToken(AP::COMMENT_TOKEN, 'aa');
		$this->subject->pushToken(AP::COMMENT_TOKEN, 'bb');
		$this->subject->finishLine();
		$this->assertContains($expect, $this->subject->finish());
	}

	/**
	 *
	 */
	public function tokensDataProvider()
	{
		return [
			[AP::COMMENT_CONTEXT_TOKEN, Formatter::COMMENT_CLASS,],
			[AP::COMMENT_TOKEN, Formatter::COMMENT_CLASS,],
			[AP::CONDITION_TOKEN, Formatter::CONDITION_CLASS,],
			[AP::IGNORED_TOKEN, Formatter::IGNORED_CLASS,],
			[AP::KEYS_POSTSPACE_TOKEN, Formatter::KEYS_POSTSPACE_CLASS,],
			[AP::KEYS_TOKEN, Formatter::KEYS_CLASS,],
			[AP::OPERATOR_POSTSPACE_TOKEN,
			Formatter::OPERATOR_POSTSPACE_CLASS,],
			[AP::OPERATOR_TOKEN, Formatter::OPERATOR_CLASS,],
			[AP::PRESPACE_TOKEN, Formatter::PRESPACE_CLASS,],
			[AP::VALUE_CONTEXT_TOKEN, Formatter::VALUE_CLASS,],
			[AP::VALUE_COPY_TOKEN, Formatter::VALUE_COPY_CLASS,],
			[AP::VALUE_TOKEN, Formatter::VALUE_CLASS,],
		];
	}

	/**
	 * @test
	 * @dataProvider tokensDataProvider
	 */
	public function tokens($tokenClass, $cssClass)
	{
		$this->subject->pushToken($tokenClass, '');
		$this->subject->finishLine();
		$this->assertContains($cssClass, $this->subject->finish());
	}

	/**
	 *
	 */
	public function errorsDataProvider()
	{
		return [
			[
				AP::INVALID_LINE_ERROR, [],
				Formatter::INVALID_LINE_FORMAT
			],
			[
				AP::NEGATIVE_KEYS_LEVEL_ERROR, [],
				Formatter::NEGATIVE_KEYS_LEVEL_FORMAT
			],
			[
				AP::POSITIVE_KEYS_LEVEL_AT_CONDITION_ERROR, [3],
				Formatter::POSITIVE_KEYS_LEVEL_AT_CONDITION_FORMAT
			],
			[
				AP::POSITIVE_KEYS_LEVEL_AT_END_ERROR, [4],
				Formatter::POSITIVE_KEYS_LEVEL_AT_END_FORMAT
			],
			[
				AP::UNCLOSED_COMMENT_CONTEXT_ERROR, [],
				Formatter::UNCLOSED_COMMENT_CONTEXT_FORMAT
			],
			[
				AP::UNCLOSED_VALUE_CONTEXT_ERROR, [],
				Formatter::UNCLOSED_VALUE_CONTEXT_FORMAT
			],
		];
	}

	/**
	 * @test
	 * @dataProvider errorsDataProvider
	 */
	public function errors($errorClass, $errorArgs, $message)
	{
		$this->subject->pushError($errorClass, $errorArgs);
		$this->subject->finishLine();
		$expect = sprintf($message, $errorArgs);
		$this->assertContains($expect, $this->subject->finish());
	}

}

