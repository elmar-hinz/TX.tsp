<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Parsers;

use ElmarHinz\TypoScriptParser\Parsers\AbstractTypoScriptParser as Parser;
use ElmarHinz\TypoScriptParser\Interfaces\
    TypoScriptTokenTrackerPushInterface as TokenTracker;
use ElmarHinz\TypoScriptParser\Interfaces\
    TypoScriptParsetimeExceptionTrackerPushInterface as ExceptionTracker;

class AbstractTypoScriptParserTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $tokenTracker  = $this->getMock(TokenTracker::class);
        $exceptionTracker  = $this->getMock(ExceptionTracker::class);
        $this->parser = $this->getMockBuilder(Parser::class)
            ->setMethods(['parse'])->getMock();
        $this->parser->injectTokenTracker($tokenTracker);
        $this->parser->injectExceptionTracker($exceptionTracker);
    }

    /**
     * @test
     */
    public function construct()
    {
        $this->assertNotNull($this->parser);
    }

}
