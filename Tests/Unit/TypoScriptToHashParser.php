<?php

namespace ElmarHinz\TypoScript\Tests\Unit;

require_once("vendor/autoload.php");

use \ElmarHinz\TypoScript\Tests\Unit\Fixtures\TypoScriptExamples as Examples;

class TypoScriptToHashParserTest extends \PHPUnit_Framework_TestCase
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
		$this->assertSame($hash, $this->parser->parse());
	}

	public function tsProvider()
	{
		return Examples::getExamples();
	}
}

