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
create the large ``TypoScript`` tree consisting of hundreds of PHP arrays on
the binary level. ``TypoScript`` was rewritten to a valid ``JSON`` string as
input.

Unfortunately ``json_decode`` does merging but not recursive merging.  As
overwriting is a feature of ``TypoScript`` this requires to prepare the
``JSON`` rendering by any approach to do the overwriting in advance. An array
was created, containing the full object path as key and the value as value to
solve this. Although this creates no nested tree, it takes time.

Together with the conversion to a ``JSON`` string in the second step, there is
no advantage in speed. Taking the non-recursive approach to handle the two
steps, it ends up in a similar speed as the ``Original TypoScript Parser``.


