<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Formatters;

use ElmarHinz\TypoScriptParser\Interfaces\
    TypoScriptTokenTagInterface as Token;
use Exception;
use ElmarHinz\TypoScriptParser\Interfaces\
    TypoScriptTokenTrackerPullInterface as TokenTracker;
use ElmarHinz\TypoScriptParser\Interfaces\
    TypoScriptParsetimeExceptionTrackerPullInterface as ExceptionTracker;
use ElmarHinz\TypoScriptParser\Formatters\TypoScriptSyntaxHighlightFormatter
    as Formatter;

class TypoScriptSyntaxHighlightFormatterTest
    extends \PHPUnit_Framework_TestCase
{

    protected function getToken($value)
    {
        $token  = $this->getMock(Token::class);
        $token->method('toTag')->willReturn('<t>' . $value . '</t>');
        return $token;
    }

    protected function getEmptyTokenTracker()
    {
        return $this->getMock(TokenTracker::class);
    }

    protected function getThreeLinesTokenTracker()
    {
        $line1 = [ $this->getToken('1.a'), $this->getToken('1.b') ];
        $line2 = [];
        $line3 = [ $this->getToken('3.a'), $this->getToken('3.b') ];

        $tracker  = $this->getMock(TokenTracker::class);
        $tracker->method('valid')
            ->will($this->onConsecutiveCalls(true, true, true, false));
        $tracker->method('key')
            ->will($this->onConsecutiveCalls(1, 2, 3));
        $tracker->method('current')
            ->will($this->onConsecutiveCalls($line1, $line2, $line3));
        return $tracker;
    }

    protected function getEmptyExceptionTracker()
    {
        $tracker  = $this->getMock(ExceptionTracker::class);
        $tracker->method('getFinalExceptions')->willReturn([]);
        $tracker->method('getByLineNumber')->willReturn([]);
        return $tracker;
    }

    protected function getThreeLinesExceptionTracker()
    {
        $map = [
            [0, []],
            [1, [new Exception('e1.a'), new Exception('e1.b')]],
            [2, []],
            [3, [new Exception('e3.a'), new Exception('e3.b')]],
        ];
        $tracker  = $this->getMock(ExceptionTracker::class);
        $tracker->method('getByLineNumber')->will($this->returnValueMap($map));
        $tracker->method('getFinalExceptions')->willReturn([]);
        return $tracker;
    }

    protected function getFinalExceptionTracker()
    {
        $finalExceptions = [new Exception('f.1'), new Exception('f.2')];
        $tracker  = $this->getMock(ExceptionTracker::class);
        $tracker->method('getByLineNumber')->willReturn([]);
        $tracker->method('getFinalExceptions')->willReturn($finalExceptions);
        return $tracker;
    }

	public function setup()
	{
		$this->formatter = new Formatter();
	}

	/**
	 * @test
	 */
    public function emptyDocument()
    {
        $this->formatter->injectTokenTracker($this->getEmptyTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getEmptyExceptionTracker());
		$expect = '<pre class="ts-hl"></pre>';
		$this->assertSame($expect, $this->formatter->format());
    }

	/**
	 * @test
	 */
    public function getLines()
    {
        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getEmptyExceptionTracker());
        $this->formatter->hideLineNumbers();
		$result = $this->formatter->format();
        $expectations = [
            '<pre class="ts-hl">',
            '<t>1.a</t>',
            '<t>1.b</t>',
            "\n\n",
            '<t>3.a</t>',
            '<t>3.b</t>',
            '</pre>',
        ];
        foreach($expectations as $expect)
            $this->assertContains($expect, $result);
    }

	/**
	 * @test
	 */
    public function defaultBehaviourShowsLineNumbersStartingWithOne()
    {
        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getThreeLinesExceptionTracker());
		$result = $this->formatter->format();
        $expectations = [
            '<pre class="ts-hl"><span class="ts-linenum">   1</span>',
            '<strong> ERROR:</strong>',
        ];
        foreach($expectations as $expect)
            $this->assertContains($expect, $result);
    }

	/**
	 * @test
	 */
    public function hideandShowLineNumberSwitchesWork()
    {
        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getThreeLinesExceptionTracker());
        $this->formatter->hideLineNumbers();
		$result = $this->formatter->format();
        $notExpect = '<span class="ts-linenum">';
        $this->assertNotContains($notExpect, $result);
        $expect = '<strong> ERROR (line 1):</strong>';
        $this->assertContains($expect, $result);

        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getThreeLinesExceptionTracker());
        $this->formatter->showLineNumbers();
		$result = $this->formatter->format();
        $expect = '<span class="ts-linenum">   1</span>';
        $this->assertContains($expect, $result);
        $expect = '<strong> ERROR:</strong>';
        $this->assertContains($expect, $result);
    }

	/**
	 * @test
	 */
    public function getLineExceptions()
    {
        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getThreeLinesExceptionTracker());
        $this->formatter->showLineNumbers();
		$result = $this->formatter->format();
        $expectations = [
            '<span class="ts-error"><strong> ERROR:</strong> e1.a e1.b</span>',
            '<span class="ts-error"><strong> ERROR:</strong> e3.a e3.b</span>',
        ];
        foreach($expectations as $expect)
            $this->assertContains($expect, $result);
    }

	/**
	 * @test
	 */
    public function getLineExceptionsWithNumbers()
    {
        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getThreeLinesExceptionTracker());
        $this->formatter->hideLineNumbers();
		$result = $this->formatter->format();
        $expectations = [
            '<span class="ts-error"><strong> ERROR (line 1):</strong>' .
           ' e1.a e1.b</span>',
            '<span class="ts-error"><strong> ERROR (line 3):</strong>' .
           ' e3.a e3.b</span>',
        ];
        foreach($expectations as $expect)
            $this->assertContains($expect, $result);
    }

	/**
	 * @test
	 */
    public function showLineNumberswithBaseline1000()
    {
        $expectations = [
            '<pre class="ts-hl"><span class="ts-linenum">   1|1000</span>',
            '<span class="ts-linenum">   3|1002</span>',
            '<strong> ERROR:</strong>',
        ];
        $this->formatter->setNumberOfBaseLine(1000);
        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getThreeLinesExceptionTracker());
		$result = $this->formatter->format();
        foreach($expectations as $expect)
            $this->assertContains($expect, $result);
    }

	/**
	 * @test
	 */
    public function hideLineNumberswithBaseline1000()
    {
        $this->formatter->hideLineNumbers();
        $this->formatter->setNumberOfBaseLine(1000);
        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getThreeLinesExceptionTracker());
		$result = $this->formatter->format();
        $exclusion = '<strong> ERROR:</strong>';
        $this->assertNotContains($exclusion, $result);
        $exclusion = '<span class="ts-linenum">';
        $this->assertNotContains($exclusion, $result);
        $expect = '<strong> ERROR (line 1|1000):</strong>';
        $this->assertContains($expect, $result);
    }

	/**
	 * @test
	 */
    public function getFinalExceptions()
    {
        $this->formatter->injectTokenTracker(
            $this->getThreeLinesTokenTracker());
        $this->formatter->injectExceptionTracker(
            $this->getFinalExceptionTracker());
		$result = $this->formatter->format();
        $expectations = [
            '<span class="ts-error"><strong>' .
            ' ERRORS AT END OF TEMPLATE:</strong> f.1 f.2</span>',
        ];
        foreach($expectations as $expect)
            $this->assertContains($expect, $result);
    }
}

