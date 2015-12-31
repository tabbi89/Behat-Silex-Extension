<?php

namespace Tabbi89\Behat\SilexExtension\Manager;

use Silex\Application;

final class ApplicationManager
{
    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @var Application
     */
    private $application;

    /**
     * @param Parameters $parameters
     */
    public function __construct(Parameters $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return Application
     *
     * @throws \RuntimeException When application was not booted
     */
    public function getBootedApplication()
    {
        if (!$this->application) {
            throw new \RuntimeException('Application was not booted');
        }

        return $this->application;
    }

    /**
     * @return Application
     */
    public function boot()
    {
        if ($this->parameters->isAppClassFilled()) {
            $class = $this->parameters->getAppClass();
            $this->application = new $class([
                'debug' => $this->parameters->isDebugEnabled(),
                'env' => $this->parameters->getEnv()
            ]);
            $this->application->boot();

            return $this->application;
        }

        $this->application = require $this->parameters->getApp();
        $this->application['debug'] = $this->parameters->isDebugEnabled();
        $this->application['env'] = $this->parameters->getEnv();
        $this->application['session.test'] = $this->parameters->isTestSessionEnabled();
        $this->application->boot();

        return $this->application;
    }
}
