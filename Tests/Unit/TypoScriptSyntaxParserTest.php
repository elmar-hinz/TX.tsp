<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit;

use \ElmarHinz\TypoScriptParser\Tests\Unit\Fixtures\TypoScriptExamples as Examples;
use \ElmarHinz\TypoScriptParser\TypoScriptSyntaxParser as Parser;
use \ElmarHinz\TypoScriptParser\AbstractTypoScriptParser as AP;
use \ElmarHinz\TypoScriptParser\TypoScriptFormatterInterface as Formatter;

class TypoScriptSyntaxParserTest extends \PHPUnit_Framework_TestCase
{

	public function setup()
	{
		$this->formatter = $this->getMockBuilder(Formatter::class)->getMock();
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
			[1, AP::PRESPACE_TOKEN, '']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(1))->method('finishLine')
            ->with(1);
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
			[1, AP::PRESPACE_TOKEN, ' '],
			[1, AP::CONDITION_TOKEN, '[CONDITION]']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(1))->method('finishLine')
            ->with(1);
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
			[1,AP::PRESPACE_TOKEN, ' '],
			[1,AP::COMMENT_TOKEN, '# comment']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(1))->method('finishLine')
            ->with(1);
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
			[1, AP::PRESPACE_TOKEN, ''],
			[1, AP::KEYS_TOKEN, 'one'],
			[1, AP::KEYS_POSTSPACE_TOKEN, ' '],
			[1, AP::OPERATOR_TOKEN, '='],
			[1, AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[1, AP::VALUE_TOKEN, '1 ']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(1))->method('finishLine')
            ->with(1);
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
			[1, AP::PRESPACE_TOKEN, ' '],
			[1, AP::KEYS_TOKEN, 'one'],
			[1, AP::KEYS_POSTSPACE_TOKEN, ' '],
			[1, AP::OPERATOR_TOKEN, '='],
			[1, AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[1, AP::VALUE_TOKEN, '1 ']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(1))->method('finishLine')
            ->with(1);
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
			[1, AP::PRESPACE_TOKEN, ' '],
			[1, AP::KEYS_TOKEN, 'one.two'],
			[1, AP::KEYS_POSTSPACE_TOKEN, ' '],
			[1, AP::OPERATOR_TOKEN, '{'],
			[1, AP::IGNORED_TOKEN, ' '],
			[2, AP::PRESPACE_TOKEN, '     '],
			[2, AP::KEYS_TOKEN, 'three'],
			[2, AP::KEYS_POSTSPACE_TOKEN, ' '],
			[2, AP::OPERATOR_TOKEN, '='],
			[2, AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[2, AP::VALUE_TOKEN, '3 '],
			[3, AP::PRESPACE_TOKEN, ' '],
			[3, AP::OPERATOR_TOKEN, '}'],
			[3, AP::IGNORED_TOKEN, ' ']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(3))->method('finishLine')
            ->withConsecutive([1],[2],[3]);
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
			[1, AP::PRESPACE_TOKEN, ''],
			[1, AP::KEYS_TOKEN, 'one'],
			[1, AP::KEYS_POSTSPACE_TOKEN, ' '],
			[1, AP::OPERATOR_TOKEN, ':='],
			[1, AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[1, AP::VALUE_TOKEN, 'append(xxx)']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(1))->method('finishLine')
            ->with(1);
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
			[1, AP::PRESPACE_TOKEN, ''],
			[1, AP::KEYS_TOKEN, 'one'],
			[1, AP::KEYS_POSTSPACE_TOKEN, ' '],
			[1, AP::OPERATOR_TOKEN, '<'],
			[1, AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[1, AP::VALUE_COPY_TOKEN, '.two']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(1))->method('finishLine')
            ->with(1);
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
			[1, AP::PRESPACE_TOKEN, ''],
			[1, AP::KEYS_TOKEN, 'one'],
			[1, AP::KEYS_POSTSPACE_TOKEN, ' '],
			[1, AP::OPERATOR_TOKEN, '>'],
			[1, AP::OPERATOR_POSTSPACE_TOKEN, ' '],
			[1, AP::IGNORED_TOKEN, 'exess']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
        $this->formatter->expects($this->exactly(1))->method('finishLine')
            ->with(1);
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
			[1, AP::PRESPACE_TOKEN, ' '],
			[1, AP::KEYS_TOKEN, 'key'],
			[1, AP::KEYS_POSTSPACE_TOKEN, ' '],
			[1, AP::OPERATOR_TOKEN, '('],
			[1, AP::IGNORED_TOKEN, ' excess1'],
			[2, AP::VALUE_CONTEXT_TOKEN, '     content line  '],
			[3, AP::PRESPACE_TOKEN, ' '],
			[3, AP::OPERATOR_TOKEN, ')'],
			[3, AP::IGNORED_TOKEN, ' excess2']
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
			[1, AP::PRESPACE_TOKEN, ' '],
			[1, AP::COMMENT_CONTEXT_TOKEN, '/* opening'],
			[2, AP::COMMENT_CONTEXT_TOKEN, '     content line  '],
			[3, AP::COMMENT_CONTEXT_TOKEN, ' */'],
			[3, AP::IGNORED_TOKEN, ' excess2']
		);
		$this->formatter->expects($this->exactly(0))->method('pushError');
		$this->formatter->expects($this->exactly(3))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

	/**
	 * @test
	 */
	public function negative_keys_level_error()
	{
		$typoScript = [
			' one.two { ',
			'         } ',
			'     } ',
			' } ',
		];
		$this->parser->appendTemplate($typoScript);
		$this->formatter->expects($this->exactly(2))->method('pushError')
            ->withConsecutive(
                [3, AP::NEGATIVE_KEYS_LEVEL_ERROR],
                [4, AP::NEGATIVE_KEYS_LEVEL_ERROR]
            );
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
		$this->formatter->expects($this->exactly(1))->method('pushFinalError')
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
			->with(5, AP::POSITIVE_KEYS_LEVEL_AT_CONDITION_ERROR, 2);
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
		$this->formatter->expects($this->exactly(1))->method('pushFinalError')
			->with(AP::UNCLOSED_VALUE_CONTEXT_ERROR);
		$this->formatter->expects($this->exactly(2))->method('finishLine');
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
		$this->formatter->expects($this->exactly(1))->method('pushFinalError')
			->with(AP::UNCLOSED_COMMENT_CONTEXT_ERROR);
		$this->formatter->expects($this->exactly(2))->method('finishLine');
		$this->formatter->expects($this->exactly(1))->method('finish');
		$this->parser->parse();
	}

}

