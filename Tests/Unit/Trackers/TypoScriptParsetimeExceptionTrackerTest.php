<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Trackers;

use ElmarHinz\TypoScriptParser\Trackers\TypoScriptParsetimeExceptionTracker
    as ExceptionTracker;
use ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException;

class TypoScriptParsetimeExceptionTrackerTest extends \PHPUnit_Framework_TestCase
{

    protected $tracker;
    protected $templateException;
    protected $lineException;

    public function setUp()
    {
        $this->tracker = new ExceptionTracker();
        $this->templateException
            = $this->getMockBuilder(TypoScriptParsetimeException::class)
            ->setMethods(null)->setConstructorArgs([false])->getMock();
        $this->lineException
            = $this->getMockBuilder(TypoScriptParsetimeException::class)
            ->setMethods(null)->setConstructorArgs([10])->getMock();
    }

    /**
     * @test
     */
    public function constructor()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function templateException()
    {
        $this->assertSame([], $this->tracker->getFinalExceptions());
        $this->tracker->push($this->templateException);
        $this->assertSame([$this->templateException],
            $this->tracker->getFinalExceptions());
    }

    /**
     * @test
     */
    public function lineException()
    {
        $this->assertSame([], $this->tracker->getByLineNumber(10));
        $this->tracker->push($this->lineException);
        $this->assertSame([$this->lineException],
            $this->tracker->getByLineNumber(10));
    }

}

