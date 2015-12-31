<?php

namespace Tabbi89\Behat\SilexExtension\Listener;

use Behat\Mink\Mink;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tabbi89\Behat\SilexExtension\Driver\KernelDriver;
use Tabbi89\Behat\SilexExtension\Manager\ApplicationManager;

/**
 * Mink sessions listener.
 * Listens Behat events and configures Silex sessions.
 */
class SessionsListener implements EventSubscriberInterface
{
    /**
     * @var ApplicationManager
     */
    private $manager;

    /**
     * @var Mink
     */
    private $mink;

    /**
     * @param ApplicationManager $manager
     * @param Mink $mink
     */
    public function __construct(ApplicationManager $manager, Mink $mink)
    {
        $this->manager = $manager;
        $this->mink = $mink;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScenarioTested::BEFORE => array('rebootSessionDriver', 10),
            ExampleTested::BEFORE  => array('rebootSessionDriver', 10)
        );
    }

    public function rebootSessionDriver()
    {
        $driver = $this->mink->getSession('silex')->getDriver();

        if ($driver instanceof KernelDriver) {
            $driver->reboot($this->manager->getBootedApplication());
        }
    }
}
