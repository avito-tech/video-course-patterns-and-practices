<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

App\WarmUp::warmUpDI();
App\Bootstrap::init()->send();
