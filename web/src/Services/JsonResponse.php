<?php

namespace App\Services;

class JsonResponse
{

    public static function json(array $data, int $statusCode = 200): false|string
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        return json_encode($data);
    }

}
