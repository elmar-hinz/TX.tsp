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

* FE: TypoScriptConditionsProcessor
* FE: TypoScriptProductionParser
* BE: TypoScriptSyntaxParser

What it is not
==============

No Boost in Performance
-----------------------

The parser is approximately twice as fast as the origional parser, but there
will be no boost of performance, as TypoScript parsing takes just a few
milliseconds at all. You won't feel a difference in speed.

What it is
==========

Better error detection of the syntax parser
-------------------------------------------

The syntax parser does already a more precise error detection, than the
origional parser, although the goal of the current milistone is to substitute
the functionality of the original.

   * Closing brace in excess.
   * Unclosed braces at condition line.
   * Unclosed multiline comment at condition line.
   * Unclosed multiline value at condition line.
   * Unclosed braces at end of template.
   * Unclosed multiline comment at end of template.
   * Unclosed multiline value at  end of template.

Having done the prove of concept that the replacement can be, advanced
debugging features are planned:

   * What aditional types of errors can be detected at parsetime already?
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

A modern parser makes it more easy to get rid of flaws in TypoScript and to add
new features like if-else conditions, that work the way you are used to from
other languages or enhance error debugging.

Standalone Usage
----------------

It's possible to use the TypoScript parser standalone outside of the TYPO3 CMS
if you like the TypoScript syntax and want to use it for configuration in other
fields.

Condition Preprocessor
----------------------

Condition evaluation is done by a preprocessor. By separtion of the condition
preprocessing it becomes possible to use the TypoScript parser without
bothering with conditions and focus on it's task.

On the other hand by isolating the conditions it becomes possible to enhance
the condition preprocessing easily. For example it becomes easy to introduce an
[ELSEIF] element.

As with the old parser the condition matching is handled by a third object.
Exchanging this object enables the development of conditions, that address a
completly different field than the TYPO3 CMS.

Public Presentation
-------------------

This is a public presentation of the parser. Should it replace the old
parser of the core? If yes, it needs to be tested in the wild before until
it is really stable. This is the extension to do so.

Differences
===========

* Backslash doesn't escape anything.
* Escaping of dots in object keys is not supported.
* Backslash is an allowed character in the keys (for PHP namespaces).


