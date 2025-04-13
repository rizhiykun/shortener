<?php

namespace App\Tests\Factory;

use App\Entity\ShortUrl;
use App\Enum\StatusType;
use App\Factory\ShortLinkFactory;
use App\Service\ShortLinkGenerator;
use PHPUnit\Framework\TestCase;

class ShortLinkFactoryTest extends TestCase
{
    private ShortLinkFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ShortLinkFactory();
    }

    /**
     * @covers \App\Factory\ShortLinkFactory
     */
    public function testCreate()
    {
        $originalUrl = 'https://example.com';
        $shortUrl = $this->factory->create($originalUrl);

        $this->assertEquals($originalUrl, $shortUrl->getOriginalUrl());
        $this->assertEquals(StatusType::GENERATING, $shortUrl->getStatus());
        $this->assertNull($shortUrl->getShortCode());
    }
}
