<?php

namespace ElmarHinz\TypoScript\Tests\Unit;

use \ElmarHinz\TypoScript\Tests\Unit\Fixtures\TypoScriptExamples as Examples;
use \ElmarHinz\TypoScript\TypoScriptSyntaxParser as Parser;
use \ElmarHinz\TypoScript\AbstractTypoScriptParser as AP;

class TypoScriptSyntaxParserTest extends \PHPUnit_Framework_TestCase
{
	const FORMATTER = '\\ElmarHinz\\TypoScript\\TypoScriptFormatterInterface';

	public function setup()
	{
		$this->formatter = $this->getMockBuilder(self::FORMATTER)->getMock();
		$this->parser = new Parser();
		$this->parser->injectFormatter($this->formatter);
	}

	/**
	 * @test
	 */
	public function emptyString()
	{
		$typoScript = ''; // matched by VOID_REGEX
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, '']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function condition()
	{
		$typoScript = ' [CONDITION]';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ' '],
			[AP::CONDITION_TOKEN, '[CONDITION]']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function comment()
	{
		$typoScript = ' # comment';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ' '],
			[AP::COMMENT_TOKEN, '# comment']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function assignmentWithoutPrespace()
	{
		$typoScript = 'one = 1 ';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ''],
			[AP::KEYS_TOKEN, 'one'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '='],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::VALUE_TOKEN, '1 ']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function assignment()
	{
		$typoScript = ' one = 1 ';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ' '],
			[AP::KEYS_TOKEN, 'one'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '='],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::VALUE_TOKEN, '1 ']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
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
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ' '],
			[AP::KEYS_TOKEN, 'one.two'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '{'],
			[AP::IGNORED_TOKEN, ' '],
			[AP::PRESPACE_TOKEN, '     '],
			[AP::KEYS_TOKEN, 'three'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '='],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::VALUE_TOKEN, '3 '],
			[AP::PRESPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '}'],
			[AP::IGNORED_TOKEN, ' ']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(3))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function modify()
	{
		$typoScript = 'one := append(xxx)';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ''],
			[AP::KEYS_TOKEN, 'one'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, ':='],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::VALUE_TOKEN, 'append(xxx)']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function copy()
	{
		$typoScript = 'one < .two';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ''],
			[AP::KEYS_TOKEN, 'one'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '<'],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::VALUE_COPY_TOKEN, '.two']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function delete()
	{
		$typoScript = 'one > exess';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ''],
			[AP::KEYS_TOKEN, 'one'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '>'],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::IGNORED_TOKEN, 'exess']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function value_context()
	{
		$typoScript = [
			' key ( excess1',
			'     content line  ',
			' ) excess2',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ' '],
			[AP::KEYS_TOKEN, 'key'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '('],
			[AP::IGNORED_TOKEN, ' excess1'],
			[AP::VALUE_CONTEXT_TOKEN, '     content line  '],
			[AP::PRESPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, ')'],
			[AP::IGNORED_TOKEN, ' excess2']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(3))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function comment_context()
	{
		$typoScript = [
			' /* opening',
			'     content line  ',
			' */ excess2',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ' '],
			[AP::COMMENT_CONTEXT_TOKEN, '/* opening'],
			[AP::COMMENT_CONTEXT_TOKEN, '     content line  '],
			[AP::COMMENT_CONTEXT_TOKEN, ' */'],
			[AP::IGNORED_TOKEN, ' excess2']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(3))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function negative_keys_level_errror()
	{
		$typoScript = [
			' one.two { ',
			'         } ',
			'     } ',
			' } ',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->expects($this->exactly(2))->method('pushError')
			->with(AP::NEGATIVE_KEYS_LEVEL_ERRROR);
		$this->formatter->expects($this->exactly(4))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function positive_keys_level_at_end_error()
	{
		$typoScript = [
			' one.two { ',
			'   three.four { ',
			'     five.six { ',
			'         } ',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->expects($this->exactly(1))->method('pushError')
			->with(AP::POSITIVE_KEYS_LEVEL_AT_END_ERROR, 2);
		$this->formatter->expects($this->exactly(4))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function positive_keys_level_at_condition_error()
	{
		$typoScript = [
			' one.two { ',
			'   three.four { ',
			'     five.six { ',
			'         } ',
			' [ELSE] ',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->expects($this->exactly(1))->method('pushError')
			->with(AP::POSITIVE_KEYS_LEVEL_AT_CONDITION_ERROR, 2);
		$this->formatter->expects($this->exactly(5))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function unclosed_value_context_at_end_error()
	{
		$typoScript = [
			' one ( ',
			'   content',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->expects($this->exactly(1))->method('pushError')
			->with(AP::UNCLOSED_VALUE_CONTEXT_AT_END_ERROR);
		$this->formatter->expects($this->exactly(2))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function unclosed_value_context_at_condition_error()
	{
		$typoScript = [
			' one ( ',
			'   content',
			' [ELSE] ',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->expects($this->exactly(1))->method('pushError')
			->with(AP::UNCLOSED_VALUE_CONTEXT_AT_CONDITION_ERROR);
		$this->formatter->expects($this->exactly(3))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function unclosed_comment_context_at_end_error()
	{
		$typoScript = [
			' /* content ',
			'   content',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->expects($this->exactly(1))->method('pushError')
			->with(AP::UNCLOSED_COMMENT_CONTEXT_AT_END_ERROR);
		$this->formatter->expects($this->exactly(2))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function unclosed_comment_context_at_condition_error()
	{
		$typoScript = [
			' /* content ',
			'   content',
			' [ELSE] ',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->expects($this->exactly(1))->method('pushError')
			->with(AP::UNCLOSED_COMMENT_CONTEXT_AT_CONDITION_ERROR);
		$this->formatter->expects($this->exactly(3))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

}

