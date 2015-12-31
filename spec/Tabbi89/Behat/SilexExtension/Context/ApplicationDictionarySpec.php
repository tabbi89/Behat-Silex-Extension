<?php

namespace spec\Tabbi89\Behat\SilexExtension\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Silex\Application;
use Tabbi89\Behat\SilexExtension\Context\ApplicationDictionary;

class ApplicationDictionarySpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf(DummyContext::class);
    }

    function it_throws_exception_when_application_is_not_set()
    {
        $this->shouldThrow(\LogicException::class)->during('getService', ['service']);
    }

    function it_gets_existing_service()
    {
        $application = new Application();
        $application['service'] = function () {
            return new DummyService();
        };

        $this->setApplication($application);
        $this->getService('service')->shouldReturnAnInstanceOf(DummyService::class);
    }
}

class DummyService
{
}

class DummyContext
{
    use ApplicationDictionary;
}