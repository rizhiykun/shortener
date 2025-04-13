<?php

namespace App\Factory;

use App\Entity\ShortUrl;
use App\Enum\StatusType;

class ShortLinkFactory
{
    public function create(string $originalUrl): ShortUrl
    {
        $shortUrl = new ShortUrl();
        $shortUrl
            ->setOriginalUrl($originalUrl)
            ->setStatus(StatusType::GENERATING);
        return $shortUrl;
    }
}
