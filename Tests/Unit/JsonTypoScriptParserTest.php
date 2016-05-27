<?php

namespace ElmarHinz\TypoScript\Tests\Unit;

require_once("vendor/autoload.php");
use \ElmarHinz\TypoScript\Tests\Unit\Fixtures\FirstTypoScriptExamples as Examples;
use \ElmarHinz\TypoScript\JsonTypoScriptParser as Parser;

class JsonTypoScriptParserTest extends \PHPUnit_Framework_TestCase
{

	public function setup()
	{
		$this->parser = new Parser();
	}

	/**
	 * @test
	 */
	public function encode_decode()
	{
		$array = ["a" => ["aa" => 1, "ab" => 2]];
		$json = json_encode($array);
		$this->assertEquals($array, json_decode($json, true));
	}

	/**
	 * @XXtest
	 */
	public function helloWorld()
	{
		$input = " hello.world = :) ";
		$expected = ['hello.' => ['world' => ':)']];
		$this->parser->appendTemplate($input);
		$result = $this->parser->parse();
		/* $result = json_decode($result, true); */
		$this->assertEquals($expected, $result);
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $hash, $tree)
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

