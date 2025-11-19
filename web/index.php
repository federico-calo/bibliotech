<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../settings.php';

use App\Core\App;
use App\Core\ErrorHandler;

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
session_regenerate_id(true);
set_exception_handler(ErrorHandler::handleException(...));
set_error_handler(ErrorHandler::handleError(...));

$app = App::initialize($settings ?? []);
$app->run();
