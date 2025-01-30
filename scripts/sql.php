<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../settings.php';

use App\Core\App;
use App\Core\Database;

if (!empty($filenames) && !empty($message)) {
    $app = App::initialize($settings ?? []);
    $connexion = new Database();
    foreach ($filenames as $filename) {
        $path = __DIR__ . '/../data/' . $filename;
        $sql = file_get_contents($path);
        $sqlRequests = array_filter(
            explode(separator: ';', string: $sql)
        );
        foreach ($sqlRequests as $sqlRequest) {
            $connexion->query($sqlRequest);
        }
    }

    echo $message;
}