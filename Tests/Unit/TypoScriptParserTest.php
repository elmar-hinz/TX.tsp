<?php

require_once("vendor/autoload.php");

use \ElmarHinz\Tests\Unit\Fixtures\TypoScriptExamples as Examples;

class TypoScriptParserTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->parser = new \ElmarHinz\TypoScriptParser();
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $tree)
	{
		$this->parser->appendTemplate($input);
		$result = $this->parser->parse();
		$this->assertEquals($tree, $result);
	}

	public function tsProvider()
	{
		return Examples::getExamples();
	}

}

