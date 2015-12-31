# BehatSilexExtension

This extension offers an easy way to begin testing Silex applications with Behat. If You are familiar with Symfony2Extension for behat then setup with this extension
will be very simple. Like the extension mentioned above it uses bare Application do it doesn't depend on anything else like Goutte so it is just faster in usage.
On each scenario we have fresh new instance of Application. Extension offers `ApplicationDictionary` which allows to access Your application.

[![Build Status](https://travis-ci.org/tabbi89/Behat-Silex-Extension.svg?branch=master)](http://travis-ci.org/tabbi89/Behat-Silex-Extension)

Installation
------------

``` bash
composer require behat/behat behat/mink behat/mink-extension tabbi89/behat-silex-extension --dev
```

Usage
-----

Next, within your project root, create a `behat.yml` file, and add:

```YAML
default:
    extensions:
        Tabbi89\Behat\SilexExtension:
            kernel:
                bootstrap: app/autoload.php
                class: Tabbi89\Behat\SilexExtension\Tests\TestApp\App\Application
                env: test
                debug: true
                testSession: true
        Behat\MinkExtension:
            default_session: silex
            sessions:
                silex:
                    silex: ~
```

Here, is where we reference the Silex extension, and tell Behat to use it as our default session. Extension allows some basic configuration values
which will be passed to Your application (`env`, `debug`, `testSession`)

If You want to use extension with Your file as main point:

```YAML
default:
    extensions:
        Tabbi89\Behat\SilexExtension:
            kernel:
                bootstrap: app/autoload.php
                path: app/Application.php
                env: test
                debug: true
                testSession: true
        Behat\MinkExtension:
            default_session: silex
            sessions:
                silex:
                    silex: ~
```

If You have more questions about application looks check examples in `tests/app/*` and configuration `tests/behat.yml.dist`

### ApplicationDictionary

Just include `ApplicationDictionary` trait in Your FeatureContext to access Your application:

``` PHP

use Behat\Behat\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Tabbi89\Behat\SilexExtension\Context\ApplicationAwareContext;
use Tabbi89\Behat\SilexExtension\Context\ApplicationDictionary;

class FeatureContext implements SnippetAcceptingContext, ApplicationAwareContext
{
    use ApplicationDictionary;

    /**
     * @Given I setup application
     */
    public function iSetupApplication()
    {
        $session = $this->getService('session');
        $application = $this->getApp();
        // ...
    }
}
```

License
-------

This bundle is under the MIT license.