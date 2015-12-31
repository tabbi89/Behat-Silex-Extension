<?php

use Behat\Behat\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Tabbi89\Behat\SilexExtension\Context\ApplicationAwareContext;
use Tabbi89\Behat\SilexExtension\Context\ApplicationDictionary;

class FeatureContext implements SnippetAcceptingContext, ApplicationAwareContext
{
    use ApplicationDictionary;

    /**
     * @Given I setup session variable :variable with value :value
     */
    public function iSetupSessionVariableWithValue($variable, $value)
    {
        $session = $this->getService('session');
        $session->set($variable, $value);
        $session->save();
    }

    /**
     * @Given I setup some application variable :variable with value :value
     */
    public function iSetupSomeApplicationVariableWithValue($variable, $value)
    {
        $application = $this->getApp();
        $application[$variable] = $value;
    }

    /**
     * @When I get session variable :variable I should get NULL
     */
    public function iGetSessionVariableIShouldGetNull($variable)
    {
        $session = $this->getService('session');

        if (!is_null($session->get($variable))) {
            throw new \RuntimeException(sprintf('Variable %s has been set and is not null', $variable));
        }
    }

    /**
     * @When application variable :variable should not be set
     */
    public function applicationVariableShouldNotBeSet($variable)
    {
        $application = $this->getApp();

        if (isset($application[$variable])) {
            throw new \RuntimeException('Variable %s is set in application', $variable);
        }
    }
}
