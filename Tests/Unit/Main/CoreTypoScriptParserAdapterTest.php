<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Main;

use ElmarHinz\TypoScriptParser\Main\CoreTypoScriptParserAdapter
    as Adapter;
use TYPO3\CMS\Backend\Configuration\TypoScript\ConditionMatching
    \ConditionMatcher as Matcher;
use ElmarHinz\TypoScriptParser\Tests\Unit\Fixtures\TypoScriptExamples
    as Examples;

if (!defined("LF")) define("LF", "\n");
if (!defined("TAB")) define("TAB", "\t");

class CoreTypoScriptParserAdapterTest extends \PHPUnit_Framework_TestCase
{

	public function setup()
	{
		$matcher = $this->getMockBuilder(Matcher::class)->getMock();
		$matcher->method('match')->will($this->returnCallback(
			function($condition) { return $condition == '[TRUE]'; }));
		$this->matcher = $matcher;
		$this->parser = new Adapter();
	}

	public function tsProvider()
	{
		return Examples::getExamples();
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $tree)
	{
		$input = implode("\n", $input);
		$this->parser->parse($input, $this->matcher);
		$result = $this->parser->setup;
		$this->assertSame($tree, $result);
	}

	public function conditionasTsProvider()
	{
		return Examples::getConditionsExamples();
	}

	/**
	 * @dataProvider conditionasTsProvider
	 * @test
	 */
	public function parseTyposcriptWithConditions($input, $tree)
	{
		$input = implode("\n", $input);
		$this->parser->parse($input, $this->matcher);
		$result = $this->parser->setup;
		$this->assertSame($tree, $result);
	}

}

