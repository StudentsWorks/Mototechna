<?php
require_once '../vendor/autoload.php';

(new Dotenv\Dotenv(dirname(__DIR__)))->load();

App\App::app(require_once '../src/config.php')->execute();
