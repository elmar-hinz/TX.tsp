<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit;

use ElmarHinz\TypoScriptParser\AbstractTypoScriptSyntaxParser
    as Parser;
use ElmarHinz\TypoScriptParser\TypoScriptTokenTrackerPushInterface
    as TokenTracker;
use ElmarHinz\TypoScriptParser\TypoScriptPasetimeExceptionTrackerPushInterface
    as ExceptionTracker;

class AbstractTypoScriptSyntaxParserTest extends \PHPUnit_Framework_TestCase
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
