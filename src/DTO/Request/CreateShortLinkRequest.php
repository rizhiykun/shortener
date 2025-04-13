<?php

namespace App\DTO\Request;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateShortLinkRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Поле original_url не может быть пустым')]
        #[Assert\Type(
            type: Type::BUILTIN_TYPE_STRING,
            message: 'Поле original_url должно быть строкой'
        )]
        #[Assert\Url(message: 'Поле original_url должно быть ссылкой')]
        public string $original_url
    ) {
    }
}
