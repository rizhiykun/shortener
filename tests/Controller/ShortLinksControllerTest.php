<?php

namespace App\Tests\Controller;

use App\DTO\Responses\CreateShortLinkResponse;
use App\Enum\StatusType;
use App\Service\ShortLinkService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ShortLinksControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Автоматическое создание базы данных и миграций перед тестами
        $command = 'php bin/console doctrine:database:create --if-not-exists --env=test';
        exec($command);

        $migrateCommand = 'php bin/console doctrine:migrations:migrate --no-interaction --env=test';
        exec($migrateCommand);
    }

    /**
     * @covers \App\Controller\ShortLinksController
     */
    public function testCreateShortLinkReturnsGeneratingOrReady(): void
    {
        $client = static::createClient();

        $shortLinkServiceMock = $this->createMock(ShortLinkService::class);
        $shortLinkServiceMock->method('process')
            ->willReturn(new CreateShortLinkResponse(StatusType::GENERATING));

        self::getContainer()->set(ShortLinkService::class, $shortLinkServiceMock);

        $client->request('GET', 'api/shortener/shortlink', [
            'original_url' => 'https://symfony.com',
        ]);

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $response);
        $this->assertEquals(StatusType::GENERATING, $response['result']['status']);
    }
}
