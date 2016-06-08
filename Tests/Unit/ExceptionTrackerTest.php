<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit;

use \ElmarHinz\TypoScriptParser\ExceptionTracker;

class ExceptionTrackerTest extends \PHPUnit_Framework_TestCase
{
    const EXCEPTION = '\ElmarHinz\TypoScriptParser\Exceptions\AbstractTypoScriptParsetimeException';

    protected $tracker;
    protected $templateException;
    protected $lineException;

    public function setUp()
    {
        $this->tracker = new ExceptionTracker();
        $this->templateException
            = $this->getMockBuilder(self::EXCEPTION)->setMethods(null)
            ->setConstructorArgs([false])->getMock();
        $this->lineException
            = $this->getMockBuilder(self::EXCEPTION)->setMethods(null)
            ->setConstructorArgs([10])->getMock();
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
        $this->assertSame([], $this->tracker->getEndOfTemplateExceptions());
        $this->tracker->push($this->templateException);
        $this->assertSame([$this->templateException],
            $this->tracker->getEndOfTemplateExceptions());
    }

    /**
     * @test
     */
    public function lineException()
    {
        $this->assertSame([], $this->tracker->getExceptionsOfLine(10));
        $this->tracker->push($this->lineException);
        $this->assertSame([$this->lineException],
            $this->tracker->getExceptionsOfLine(10));
    }

}

