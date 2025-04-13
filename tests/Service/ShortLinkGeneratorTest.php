<?php

namespace App\Tests\Service;

use App\Service\ShortLinkGenerator;
use PHPUnit\Framework\TestCase;

class ShortLinkGeneratorTest extends TestCase
{
    private ShortLinkGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new ShortLinkGenerator();
    }

    /**
     * @covers \App\Service\ShortLinkGenerator
     */
    public function testGenerateShortLinkWithDefaultLength()
    {
        $result = $this->generator->generateShortLink();

        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{4,8}$/', $result);
    }

    /**
     * @covers \App\Service\ShortLinkGenerator
     */
    public function testGenerateShortLinkWithSpecificLength()
    {
        $length = 6;
        $result = $this->generator->generateShortLink($length);

        $this->assertEquals($length, strlen($result));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{'.$length.'}$/', $result);
    }

    /**
     * @covers \App\Service\ShortLinkGenerator
     */
    public function testGenerateShortLinkContainsOnlyValidCharacters()
    {
        $result = $this->generator->generateShortLink(100);

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $result);
    }

    /**
     * @covers \App\Service\ShortLinkGenerator
     */
    public function testGenerateShortLinkIsRandom()
    {
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $results[] = $this->generator->generateShortLink(8);
        }

        // Very basic randomness check - all generated strings should be different
        $this->assertEquals(count($results), count(array_unique($results)));
    }
}
