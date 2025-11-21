<?php

namespace App\Services;

use OpenApi\Generator;
use OpenApi\Attributes as OA;

class Openapi
{
    public function __construct(
        private Generator $generator
    ) {}

    public function generate($scanPath): string
    {
        $openapi = $this->generator->generate([$scanPath]);
        return $openapi->toJson();
    }

}

#[OA\Schema(
    schema: "BooksPaginated",
    properties: [
        new OA\Property(property: "current_page", type: "integer"),
        new OA\Property(property: "total_pages", type: "integer"),
        new OA\Property(property: "total_books", type: "integer"),
        new OA\Property(
            property: "books",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Book")
        ),
    ],
    type: "object"
)]
class BooksPaginated {}



#[OA\Schema(
    schema: "BookInput",
    required: ["title", "author"],
    properties: [
        new OA\Property(property: "title", type: "string"),
        new OA\Property(property: "author", type: "string"),
        new OA\Property(property: "isbn", type: "string", nullable: true),
        new OA\Property(property: "summary", type: "string", nullable: true)
    ],
    type: "object"
)]
class BookInput {}


#[OA\Schema(
    schema: "TokenResponse",
    properties: [
        new OA\Property(property: "message", type: "string", example: "Token generated successfully"),
        new OA\Property(property: "token", type: "string", example: "a12c9d8e-f31b-456a-b7cd-9e1e2d5904af")
    ],
    type: "object"
)]
class TokenResponse {}