<?php

namespace Tabbi89\Behat\SilexExtension\Tests\TestApp\app;

use Silex\Application as SilexApplication;
use Silex\Provider\SessionServiceProvider;

class Application extends SilexApplication
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->register(new SessionServiceProvider());
        $this['session.test'] = $values['env'] === 'test';
    }
}
