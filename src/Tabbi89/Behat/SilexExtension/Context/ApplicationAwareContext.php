<?php

namespace Tabbi89\Behat\SilexExtension\Context;

use Behat\Behat\Context\Context;
use Silex\Application;

/**
 * HttpKernel aware interface for contexts.
 */
interface ApplicationAwareContext extends Context
{
    /**
     * Sets Application instance.
     *
     * @param Application $application
     */
    public function setApplication(Application $application);
}
