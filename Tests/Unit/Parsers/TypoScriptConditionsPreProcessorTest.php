<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Parsers;

use ElmarHinz\TypoScriptParser\Parsers\TypoScriptConditionsPreProcessor
    as Parser;
use TYPO3\CMS\Backend\Configuration\TypoScript\ConditionMatching\
    ConditionMatcher as Matcher;

class TypoScriptConditionsPreProcessorTest extends \PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->parser = new Parser();
		$matcher = $this->getMock(Matcher::class);
		$matcher->method('match')->will($this->returnCallback(
			function($condition) { return $condition == '[TRUE]'; }));
		$this->parser->setMatcher($matcher);
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $expect)
	{
		$this->parser->appendTemplate($input);
		$result = implode("\n", $this->parser->parse());
		$this->assertSame($expect, $result);
	}

	public function tsProvider()
	{
		return array (
			'simple' => [
				'show' . PHP_EOL,
				'show'
			],
			'true' => [
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'first' . PHP_EOL .
				'after'
			],
			'false' => [
				'before' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'after'
			],
			'true-true' => [
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'first' . PHP_EOL .
				'second' . PHP_EOL .
				'after'
			],
			'true-false' => [
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'first' . PHP_EOL .
				'after'
			],
			'false-true' => [
				'before' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'second' . PHP_EOL .
				'after'
			],
			'false-false' => [
				'before' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'after'
			],
			'true-else' => [
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[ELSE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL  .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'first' . PHP_EOL .
				'after'
			],
			'false-else' => [
				'before' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[ELSE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL  .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'second' . PHP_EOL .
				'after'
			],
			'conditions in comment context' => [
                ' /* multi line comment ' . PHP_EOL .
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL .
                ' */ end' . PHP_EOL,

                ' /* multi line comment ' . PHP_EOL .
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL .
                ' */ end',
            ],
			'conditions in value context' => [
                'multi.line.value ( ' . PHP_EOL .
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL .
                ' ) end' .PHP_EOL,

                'multi.line.value ( ' . PHP_EOL .
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL .
                ' ) end',
            ],
		);
	}

}

