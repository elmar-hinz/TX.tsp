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
		$typoScript = 'one = 1';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ''],
			[AP::KEYS_TOKEN, 'one'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '='],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::VALUE_TOKEN, '1']
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
		$typoScript = ' one = 1';
		$this->parser->appendTemplate($typoScript);
		$this->formatter->method('pushToken')->withConsecutive(
			[AP::PRESPACE_TOKEN, ' '],
			[AP::KEYS_TOKEN, 'one'],
			[AP::KEYS_POSTSPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, '='],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::VALUE_TOKEN, '1']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(1))->method('finishLine');
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
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::IGNORED_TOKEN, 'excess1'],
			[AP::VALUE_CONTEXT_TOKEN, '     content line  '],
			[AP::PRESPACE_TOKEN, ' '],
			[AP::OPERATOR_TOKEN, ')'],
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::IGNORED_TOKEN, 'excess2']
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
			[AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[AP::IGNORED_TOKEN, 'excess2']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(3))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

}

