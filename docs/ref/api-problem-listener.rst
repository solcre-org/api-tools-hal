.. _ref/api-problem-listener:

The API-Problem listener
========================

In the chapter on :ref:`error reporting <laminashal.error-reporting>`, I noted that
LaminasHal has standardized on the API-Problem format for reporting errors.

Currently, an API-Problem response will be created automatically for any of the
following conditions:

- raising a ``Laminas\ApiTools\Hal\Exception\CreationException`` inside a ``create``
  listener.
- raising a ``Laminas\ApiTools\Hal\Exception\PatchException`` inside a ``patch``
  listener.
- raising a ``Laminas\ApiTools\Hal\Exception\UpdateException`` inside an ``update``
  listener.
- raising an exception in any other ``Laminas\ApiTools\Hal\Resource`` event listener.

If the exception you raise implements
``Laminas\ApiTools\Hal\Exception\ProblemExceptionInterface`` -- which
``Laminas\ApiTools\Hal\Exception\DomainException`` does, as does its descendents, the
``CreationException``, ``PatchException``, and ``UpdateException`` -- you can
set additional details in the exception instance before throwing it, allowing
you to hit to ``Laminas\ApiTools\Hal\ApiProblem`` how to render itself.

There's another way to create and return an API-Problem payload, however: create
and return an instance of ``Laminas\ApiTools\Hal\ApiProblem`` from any of the
``Resource`` event listeners. This gives you fine-grained control over creation
of the problem object, and avoids the overhead of exceptions.

However, there's another way to receive an API-Problem result: raising an
exception. For this the listener becomes important.

The Listener
------------

``Laminas\ApiTools\Hal\Module`` registers a listener with the identifier
``Laminas\ApiTools\Hal\ResourceController`` on its ``dispatch`` event. This event then
registers the ``Laminas\ApiTools\Hal\ApiProblemListener`` on the application ``render``
event. Essentially, this ensures that the listener is only registered if a
controller intended as a RESTful resource endpoint is triggered.

The listener checks to see if the ``MvcEvent`` instance is marked as containing
an error. If so, it checks to see if the ``Accept`` header is looking for a JSON
response, and, finally, if so, it marshals an ``ApiProblem`` instance from the
exception, setting it as the result.

This latter bit, the ``Accept`` header matching, is configurable. If you want to
allow an API-Problem response for other than the default set of mediatypes
(``application/hal+json``, ``application/problem+json``, and
``application/json``), you can do so via your configuration. Set the value in
the ``accept_filter`` subkey of the ``api-tools-hal`` configuration; the value
should be a comma-separated set of mimetypes.

.. code-block:: php
    :linenos:

    return array(
        'api-tools-hal' => array(
            // ...
            'accept_filter' => 'application/json,text/json',
        ),
    );

.. index:: api-problem, error reporting, exception, accept
