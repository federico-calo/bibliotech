<?php

namespace Tests\Core;

use App\Services\JsonResponse;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{

    public function testJsonResponseWithDefaultStatusCode(): void
    {
        $data = ['message' => 'success'];
        $result = JsonResponse::json($data);
        $this->assertJson($result, 'La sortie doit être un JSON valide');
        $this->assertSame(json_encode($data), $result, 'La sortie JSON doit correspondre aux données encodées');
    }

    public function testJsonResponseWithCustomStatusCode(): void
    {
        $data = ['error' => 'not found'];
        $statusCode = 404;
        $result = JsonResponse::json($data, $statusCode);
        $this->assertJson($result, 'La sortie doit être un JSON valide');
        $this->assertSame(json_encode($data), $result, 'La sortie JSON doit correspondre aux données encodées');
    }
}
