.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt

.. _architecture:

==============================
Architecture
==============================

The major goal of the architecture is flexibility, to enable the development
of new features and to enable the user to customize the parsers to his needs.
The main devices to reach this goal are:

   * Separation of concerns
   * Programming against interfaces
   * Dependency injection
   * Classes as identifiers

Separtion of Concerns
=====================

The classes are rather small to encapsulate a single concern.

The syntax tracker is the most complex example. It focuses on the parsing
algorithm, while it delegates the representation of tokens and execptions to
dedicated classes. The collecting of tokens and exeptions is done by tracker
classes. The tracker objects are finally accessed by a formatter class to
produce the highlighted output.

Concerns represented by one class each:

* Parsing
* Representation of a token
* Representation of an exception
* Tracking tokens
* Tracking exceptions
* Formatting the report

Programming against Interfaces
==============================

Whereever two classes cooperate, there is an interface between them. A class
can have multiple interfaces, if it cooperates with multiple other classes. All
this interfaces are defined as PHP interfaces, that are stored into the folder
`Classes/Interfaces`.

A class should not depend on other classes to cooperate, but on interfaces. It
is free to cooperate with every class that implements the matching interface.
Each class can be exchanged by a customized class, as long as the customized
class provides the interfaces, that the given classes can talk to.

An example usage of this interfaces are the mock objects of the unit tests.
While testing a single class it is decoupled from other classes, by using mock
objects, that implement the interface to test against.

Dependency Injection
====================

Dependency injection is related to programming against interfaces. If a class
must not depend on other classes, it must not create classes by the keyword
``new`` itself. Instead objects, that implement the required interface, are
injected.

For sure a place is needed where all this dependency injection is done, where
the objects are created and wired up. This is done in the main application
classes that are stored in the folder ``Main/``. You can think of an
application class as a kind of configuration, that composes objects according
to your taste. You write a new one of this main configuration classes, to
compose your own application or to alter an existing one.

Classes as Identifieres
=======================

An exception from the rule, to not use the keyword ``new``, are the tokens and
exceptions. Each class is designed to serve as an identifier. You can think of
them as constants. The object is created by the keyword ``new`` as you mean
exactly it's class as identifier, not the interface. They are ``final``.

Nonetheless there is flexibilty. The exceptions and tokens are created by
parsers and you can exchange the parser creating them. That means you can
exchange the part, that contains the ``new`` keywords.

You can create your own exceptions and tokens by writing new classes. It's just
a few lines each, because they inherit almoust all from abstract classes. The
freedom to easily add new tokens and exceptions is one reason, why they are not
implemented as constants, apart from the additional functionality a class
offers.

