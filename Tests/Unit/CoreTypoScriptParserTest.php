<?php

require_once("vendor/autoload.php");
use \ElmarHinz\Tests\Unit\Fixtures\TypoScriptExamples as Examples;

if(!defined("LF")) define("LF", "\n");
if(!defined("TAB")) define("TAB", "\t");

class CoreTypoScriptParserTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->parser = new \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $hash, $tree)
	{
		$input = implode("\n", $input);
		$this->parser->parse($input);
		$result = $this->parser->setup;
		$this->assertEquals($tree, $result);
	}

	public function tsProvider()
	{
		return Examples::getExamples();
	}

}

