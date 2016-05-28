<?php

namespace ElmarHinz\TypoScript\Tests\Unit;

require_once("vendor/autoload.php");

use \ElmarHinz\TypoScript\Tests\Unit\Fixtures\TypoScriptExamples as Examples;

if(!defined("LF")) define("LF", "\n");
if(!defined("TAB")) define("TAB", "\t");

class CoreTypoScriptParserTest extends \PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->parser = new \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
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
		$this->parser->parse($input);
		$result = $this->parser->setup;
		$this->assertSame($tree, $result);
	}

}

