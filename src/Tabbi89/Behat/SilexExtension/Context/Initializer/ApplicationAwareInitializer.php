<?php

namespace Tabbi89\Behat\SilexExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tabbi89\Behat\SilexExtension\Context\ApplicationAwareContext;
use Tabbi89\Behat\SilexExtension\Manager\ApplicationManager;

/**
 * Kernel aware contexts initializer.
 * Sets Kernel instance to the KernelAware contexts.
 */
final class ApplicationAwareInitializer implements ContextInitializer, EventSubscriberInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $kernel;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var ApplicationManager
     */
    private $manager;

    /**
     * Initializes initializer.
     *
     * @param ApplicationManager $kernelManager
     */
    public function __construct(ApplicationManager $kernelManager)
    {
        $this->kernel = $kernelManager->getBootedApplication();
        $this->manager = $kernelManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScenarioTested::AFTER  => array('rebootKernel', -15),
            ExampleTested::AFTER   => array('rebootKernel', -15),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof ApplicationAwareContext && !$this->usesKernelDictionary($context)) {
            return;
        }

        $context->setApplication($this->kernel);
        $this->context = $context;
    }

    /**
     * Reboots HttpKernel after each scenario.
     */
    public function rebootKernel()
    {
        $this->kernel = $this->manager->boot();
        $this->initializeContext($this->context);
    }

    /**
     * Checks whether the context uses the ApplicationDictionary trait.
     *
     * @param Context $context
     *
     * @return boolean
     */
    private function usesKernelDictionary(Context $context)
    {
        $refl = new \ReflectionObject($context);
        if (method_exists($refl, 'getTraitNames')) {
            if (in_array('Tabbi89\Behat\\SilexExtension\\Context\\ApplicationDictionary', $refl->getTraitNames())) {
                return true;
            }
        }
        return false;
    }
}
