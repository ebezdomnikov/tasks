<?php

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

$config['db'] = '100m';
$config['username'] = 'root';
$config['password'] = '';
$config['host'] = '127.0.0.1';

$task = new \Task\Task($config);
$task->execute();