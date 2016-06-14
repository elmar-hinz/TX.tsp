<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

use ElmarHinz\TypoScriptParser\Interfaces
    \TypoScriptParsetimeExceptionInterface;

abstract class AbstractTypoScriptParsetimeException extends \Exception
    implements TypoScriptParsetimeExceptionInterface
{
    const CODE = 1465381339;
    const MESSAGE = 'A parsetime exception occured.';

    protected $templateLineNumberOrFalseForEndOfTemplate;

    /**
     * Constructor
     *
     * Parameter is the template line number of error detection, where the
     * first line of the template is starting with 1. If the error is detected
     * at the end of the template, false must be given instead.
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
                'The parameter has to be the number of a line (integer) where
                the error is detected or false for end of template exceptions.'
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

