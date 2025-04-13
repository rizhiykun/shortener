<?php

namespace App\DTO\Responses;

use App\Enum\StatusType;
use OpenApi\Attributes as OA;
use Symfony\Component\PropertyInfo\Type;

#[OA\Schema(
    properties: [
        new OA\Property(property: 'status', type: Type::BUILTIN_TYPE_STRING, example: StatusType::GENERATING),
        new OA\Property(property: 'shortUrl', type: Type::BUILTIN_TYPE_STRING),
    ],
    type: Type::BUILTIN_TYPE_OBJECT
)]
class CreateShortLinkResponse
{
    public function __construct(
        public string $status,
        public ?string $shortUrl = null
    ) {
    }
}
