<?php

namespace Tabbi89\Behat\SilexExtension\Driver;

use Behat\Mink\Driver\BrowserKitDriver;
use Symfony\Component\HttpKernel\Client;
use Silex\Application;

/**
 * Kernel driver for Mink.
 */
class KernelDriver extends BrowserKitDriver
{
    /**
     * @param Application $application
     * @param string|null $baseUrl
     */
    public function __construct(Application $application, $baseUrl = null)
    {
        parent::__construct(new Client($application), $baseUrl);
    }

    /**
     * Refresh the driver.
     *
     * @param Application $app
     * @return KernelDriver
     */
    public function reboot($app)
    {
        return $this->__construct($app);
    }
}
