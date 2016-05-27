<?php

require_once("vendor/autoload.php");
use \ElmarHinz\Tests\Unit\Fixtures\TypoScriptExamples as Examples;
use \ElmarHinz\ExtendedParser;

if(!defined("LF")) define("LF", "\n");
if(!defined("TAB")) define("TAB", "\t");

class MatchObject
{
	public function match($condition)
	{
		return $condition == '[TRUE]';
	}
}

class ExtendedParserTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->parser = new ExtendedParser;
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $tree)
	{
		$matchObject = new MatchObject();
		$input = implode("\n", $input);
		$this->parser->parse($input, $matchObject);
		$result = $this->parser->setup;
		$this->assertEquals($tree, $result);
	}

	public function tsProvider()
	{
		return Examples::getPreProcessExamples();
	}

}

