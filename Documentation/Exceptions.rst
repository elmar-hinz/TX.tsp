.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt

.. _exceptions:

============
Exceptions
============

The Exception Hierarchy
=======================

* \Exception
   * \TypoScriptParsetimeException (abstract)
      * TypoScriptBraceInExcessException
      * TypoScriptKeysException
      * TypoScriptUnclosedConditionException
      * TypoScriptBracesMissingAtConditionE
      * TypoScriptOperatorException
      * TypoScriptUnclosedValueException
      * TypoScriptBracesMissingAtEndOfT
      * TypoScriptParsetimeException
      * TypoScriptUnclosedCommentException

Where is the TypoScriptRuntimeException?
========================================

Where is a `TypoScriptParsetimeException` there should also be a
`TypoScriptRuntimeException`, shouldn't it?

TypoScript pasetime exceptions occur while parsing TypoScript into a PHP array
tree. Runtime exceptions would make sense in the ContentObjectRenderer, when
the PHP array tree is used to render the page.

Both parts are connected by the PHP array tree, but apart from that, they are
not connected. The array tree could come from a differnt source. The parser
could render an array tree for a completly different purpose.

Follows:

1.) A `TypoScriptParsetimeException` doesn't belong into the parser package.
2.) Both types of exceptions should not inherit from a common
    `TypoScriptException` to not introduce an unnecessary dependency of the
    packages. Instead both directly inherit from `\Exception`.

