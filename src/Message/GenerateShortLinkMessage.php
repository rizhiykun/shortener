<?php

namespace App\Message;

readonly class GenerateShortLinkMessage
{
    public function __construct(
        public string $shortLinkId
    ) {
    }
}
