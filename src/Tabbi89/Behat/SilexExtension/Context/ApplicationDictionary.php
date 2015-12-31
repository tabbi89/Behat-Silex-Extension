<?php

namespace Tabbi89\Behat\SilexExtension\Context;

use Silex\Application;

/**
 * Application support methods for SilexExtension.
 */
trait ApplicationDictionary
{
    /**
     * @var null|Application
     */
    private $application;

    /**
     * Sets Application instance.
     *
     * @param Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return null|Application
     */
    public function getApp()
    {
        return $this->application;
    }

    /**
     * @param string $service
     *
     * @return mixed
     *
     * @throws \LogicException When application is not set
     */
    public function getService($service)
    {
        if (!$this->application) {
            throw new \LogicException('Application is not set');
        }

        return $this->application[$service];
    }
}
