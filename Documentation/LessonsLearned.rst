.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt

.. _start:

==============================
Lessons Learned
==============================

Non-Recursive Parser
====================

The ``Non-Recursive Parser`` is the approach taken by this parser. The whole
rendering happens within one function by using simple loop structures. Calls to
itself or other methods are avoided as far as reasonable. This turns out to be
twice as fast as the recursive ``Original TypoScript Parser``.

Original TypoScript Parser
==========================

The original parser of the TYPO3 core uses recursive calls to handle the
nesting of the braces of the object name pathes.

JSON Parser
===========

The idea of the ``JSON Parser`` was, to use the PHP function ``json_decode`` to
create the large ``TypoScript`` tree consising of hundreds of PHP arrays on the
binary level. ``TypoScript`` would be rewritten to a valid ``JSON`` sting as
input.

Unfortunately ``json_decode`` does merging but not recursive merging.
As overwriting is a feature of ``TypoScript`` this requires overwriting the
values by simple path overwriting to prepare the ``JSON`` rendering. Together
with the conversion to a ``JSON`` string, there is no advantage in speed, if
both sums up. Taking the non-recursive apprach to handle the two steps, it
ends up in quite the same speed as the ``Origional TypoScript Parser``.


