<?php

$app = new Silex\Application();
$app->register(new Silex\Provider\SessionServiceProvider());

return $app;
