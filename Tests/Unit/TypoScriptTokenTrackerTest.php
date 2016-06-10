<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit;

use \ElmarHinz\TypoScriptParser\TypoScriptTokenTracker as Tracker;
use \ElmarHinz\TypoScriptParser\Tokens\AbstractTypoScriptToken  as Token;

class TypoScriptTokenTrackerTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->tracker = new Tracker();
    }

    /**
     * @test
     */
    public function testInitialState()
    {
        $this->assertSame(0, $this->tracker->getCountOfLines());
        $this->assertSame([], $this->tracker->getByLine(1));
    }

    /**
     * @test
     */
    public function writeReadCycle()
    {
        $token1 = $this->getMockbuilder(Token::class)
            ->setConstructorArgs(['value1'])->getMock();
        $token2 = $this->getMockbuilder(Token::class)
            ->setConstructorArgs(['value2'])->getMock();
        $token3 = $this->getMockbuilder(Token::class)
            ->setConstructorArgs(['value3'])->getMock();
        $this->tracker->push($token1);
        $this->tracker->push($token2);
        $this->assertSame(1, $this->tracker->getCountOfLines());
        $this->tracker->nextLine();
        $this->tracker->push($token3);
        $this->assertSame(2, $this->tracker->getCountOfLines());
        $this->tracker->nextLine();
        $this->assertSame(2, $this->tracker->getCountOfLines());
        $tokens = [];
        foreach($this->tracker as $key => $value) $tokens[$key] = $value;
        $expected = [ 1 => [$token1, $token2], 2 => [$token3] ];
        $this->assertSame($expected, $tokens);
        $this->assertSame(2,$this->tracker->getCountOfLines());
    }
}

