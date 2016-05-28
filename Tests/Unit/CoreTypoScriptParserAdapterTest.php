<?php

namespace ElmarHinz\TypoScript\Tests\Unit;

require_once("vendor/autoload.php");
use \ElmarHinz\TypoScript\Tests\Unit\Fixtures\TypoScriptExamples as Examples;
use \ElmarHinz\TypoScript\CoreTypoScriptParserAdapter as Adapter;

if(!defined("LF")) define("LF", "\n");
if(!defined("TAB")) define("TAB", "\t");

class CoreTypoScriptParserAdapterTest extends \PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$matchClass = '\\TYPO3\\CMS\\Backend\\Configuration\\TypoScript\\ConditionMatching\\ConditionMatcher';
		$matcher = $this->getMockBuilder($matchClass)->getMock();
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
		$this->assertEquals($tree, $result);
	}

	public function conditionasTsProvider()
	{
		return Examples::getPreProcessExamples();
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
		$this->assertEquals($tree, $result);
	}

}

