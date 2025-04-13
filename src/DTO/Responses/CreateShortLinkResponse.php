<?php

namespace App\DTO\Responses;

use App\Enum\StatusType;
use Symfony\Component\PropertyInfo\Type;
use OpenApi\Attributes as OA;

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
    ) {}

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'shortUrl' => $this->shortUrl
        ];
    }
}