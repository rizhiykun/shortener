<?php

namespace App\DTO\Request;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateShortLinkRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Поле originalUrl не может быть пустым')]
        #[Assert\Type(
            type: Type::BUILTIN_TYPE_STRING,
            message: 'Поле originalUrl должно быть строкой'
        )]
        #[Assert\Url(message: 'Поле originalUrl должно быть ссылкой')]
        public string $originalUrl
        ){}
}