<?php

$settings = [];
$settings['db'] = 'mysql:host=database;dbname=lamp';
$settings['mysqlUser'] = 'lamp';
$settings['mysqlPassword'] = 'lamp';
$settings['debug'] = FALSE;
$settings['routingPath'] = __DIR__ . '/web/routing.json';
$settings['templatePath'] = __DIR__ . '/web/templates/';
$settings['templateExtension'] = '.php';
$settings['hashKey'] = '80182e9f638aee95b166751eb93c07b8580717c3a7f9ce2e6485a0d3d4ac9284';
$settings['redisHost'] = 'cache';