.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt

.. _intro:

============
Introduction
============

This extensions ships a TypoScript parser, that is suited to replace the
original TypoScript parser for frontend rendering. In fact a family of
parsers has been introduced, specialized on different tasks.

* FE: TypoScriptConditionsPreProcessor
* FE: TypoScriptProductionParser
* BE: TypoScriptSyntaxParser

What it is not
==============

No Boost in Performance
-----------------------

The parsing of TypoScript just takes a few milliseconds. Hence, it's not the
primary goal to speed up the performance but to improve the architecture.
The algorithm is twice as fast as the original algorithm, but with the
split into conditions preprocessor and processor the time is about the same
again.

What it is
==========

Public Presentation
-------------------

First of all this extension is a public presentation of the rewritten parser.
Should it replace the old parser of the core? If yes, it needs to be tested in
the wild before until it is really stable.

Standalone Usage
----------------

It's possible to use the TypoScript parser outside of the TYPO3 CMS, if you
like the TypoScript syntax and want to use it for configuration in other
fields. This is possible with or without the conditions preprocessor.

Improving the error detection
-----------------------------

The error detection covers the error detection of the origional parser and
tries be be a little better already. Also the displaying of the line numbers
has been worked upon. See Screenshots!

Planned improvements in future versions:

   * CLI interface to check TS within continuous integration workflows.
   * Do syntax highlighting of conditions, instead of printing them in one
     color.
   * Detect the difference of objects and properties, because only objects are
     allowed ot be copied by reference.
   * (Related) Throw verbose errors from TS objects, catch them and and display
     them into the backend.

New Architecture
----------------

The reason to write a new TypoScript parser is, to get a modern architecture
for it:

    * easy to understand
    * easy to debug
    * easy to extend

A modern parser makes it more easy to get rid of flaws in TypoScript, enhance
error detection and add new features like if-else conditions, that work the way
you are used to from other languages.

Condition Preprocessor
----------------------

Condition evaluation has been separated into a preprocessor class. It becomes
possible to use the TypoScript parser without bothering with conditions at all
or apply different types of preprocessors. It's more simple to enhance the
condition preprocessing, as an example think of a fullblown
`IF-ELSEIF-ELSE-END` structure.

As with the old parser the condition matching is handled by a third object.
Exchanging this object enables the development of conditions, that address a
completly different field than the TYPO3 CMS.

Differences
===========

* Escaping of dots by backslash is not supported.

