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
original TypoScript parser for frontend rendering.

What it is not
==============

No Boost in Performance
-----------------------

The parser is approximately twice as fast as the origional parser, but there
will be no boost of performance, as TypoScript parsing takes just a few
milliseconds at all. You will not feel a difference.

Not Used in the Backend
-----------------------

The parser still doesn't suppurt syntax highlighting and error handling. For
now it is not used in the backend at all.

The backend features will follow in future versions.

What it is
==========

New Architecture
----------------

The reason to write a new TypoScript parser is, to get a modern architecture
for it. The architecture is clean and easy to understand. The final goal is,
to get a parser that makes it easy to develop TypoScript into the future.

    * easy to understand
    * easy to debug
    * easy to extend

Standalone Usage
----------------

It's possible to use the TypoScript parser standalone outside of the TYPO3 CMS
if you like the TypoScript syntax an want to use it for configuration in
other fields.

Condition Preprocessor
----------------------

Condition evaluation is done by a preprocessor. By separtion of the condition
preprocessing it becomes possible to use the TypoScript parser without
bothering with conditions.

On the other hand by isolating the conditions it becomes possible to enhance
the conditions easily. Nested conditions would be an example of enhancement.

As with the old parser the condition matching is handled by a third object.
This enables the development of conditions, that address a completly different
field than the TYPO3 CMS.

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


