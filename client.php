<?php
require __DIR__ . '/vendor/autoload.php';

$config = include_once __DIR__."/config/config.php";
\Task\Client::start($config);