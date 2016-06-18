<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

use ElmarHinz\TypoScriptParser\Interfaces
    \TypoScriptParsetimeExceptionInterface;

abstract class AbstractTypoScriptParsetimeException extends \Exception
    implements TypoScriptParsetimeExceptionInterface
{
    const CODE = 999;
    const MESSAGE = 'A parsetime exception occured.';

    protected $templateLineNumberOrFalseForEndOfTemplate;

    /**
     * Constructor
     *
     * Parameter is the template line number of exception detection, where the
     * first line of the template is starting with 1. If the exception is
     * detected at the end of the template, false must be given instead.
     *
     * @parem mixed $templateLineNumberOrFalse See description above.
     */
    public function __construct($templateLineNumberOrFalse)
    {
        if(is_int($templateLineNumberOrFalse)
            || $templateLineNumberOrFalse === false) {
        $this->templateLineNumberOrFalseForEndOfTemplate
            = $templateLineNumberOrFalse;
        } else {
            throw new \BadMethodCallException(
                'The parameter has to be the number of the line (integer) ' .
                'where the exception is detected or false for exceptions at ' .
                'the end of the template.'
            , 1465382523);
        }
        parent::__construct(static::MESSAGE, static::CODE);
    }

    public function getTemplateLineNumber()
    {
        if($this->isEndOfTemplateException()) {
            throw new \BadMethodCallException(
                'The method is not supported for end of template exceptions.'
            , 1465383315);
        } else {
            return $this->templateLineNumberOrFalseForEndOfTemplate;
        }
    }

    public function isEndOfTemplateException()
    {
        return $this->templateLineNumberOrFalseForEndOfTemplate === false;
    }

}

