<?php

namespace Tabbi89\Behat\SilexExtension\Manager;

class Parameters
{
    /**
     * @var null|string
     */
    private $appClass;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var string
     */
    private $env;

    /**
     * @var bool
     */
    private $testSession;

    /**
     * @var string
     */
    private $app;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->appClass = $params['appClass'];
        $this->debug = $params['debug'];
        $this->env = $params['env'];
        $this->testSession = $params['testSession'];
        $this->app = $params['app'];
    }

    /**
     * @return string
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return boolean
     */
    public function isTestSessionEnabled()
    {
        return $this->testSession;
    }

    /**
     * @return boolean
     */
    public function isDebugEnabled()
    {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function isAppClassFilled()
    {
        return !is_null($this->appClass);
    }

    /**
     * @return string
     */
    public function getAppClass()
    {
        if (!$this->isAppClassFilled()) {
            throw new \LogicException('App class is not defined');
        }

        return $this->appClass;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }
}
