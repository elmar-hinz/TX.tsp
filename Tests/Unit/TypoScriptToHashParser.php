<?php

require_once("vendor/autoload.php");

use \ElmarHinz\Tests\Unit\Fixtures\TypoScriptExamples as Examples;

class TypoScriptToHashParserTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->parser = new \ElmarHinz\TypoScriptToPlainKeysParser();
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $hash, $tree)
	{
		$this->parser->appendTemplate($input);
		$this->assertEquals($hash, $this->parser->parse());
	}

	public function tsProvider()
	{
		return Examples::getExamples();
	}
}

