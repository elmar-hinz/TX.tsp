<?php

namespace ElmarHinz\TypoScriptParser\Tokens;

use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptTokenInterface;
use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptTokenTagInterface;

abstract class AbstractTypoScriptToken
    implements TypoScriptTokenInterface, TypoScriptTokenTagInterface
{
    CONST FORMAT = '<%1$s class="%2$s">%3$s</%1$s>';

    protected $value = '';
    protected $tag = 'span';
    protected $classes = 'ts-abstract';

    public function __construct($value)
    {
        $this->setValue($value);
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setClasses($classes)
    {
        $this->classes = $classes;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toTag()
    {
        return sprintf(static::FORMAT,
            $this->tag, $this->classes, htmlspecialchars($this->value));
    }

}
