<?php

namespace App\Tests\Service;

use App\DTO\Responses\CreateShortLinkResponse;
use App\Enum\StatusType;
use App\Entity\ShortUrl;
use App\Factory\ShortLinkFactory;
use App\Lock\ShortLinkLockFactory;
use App\Message\GenerateShortLinkMessage;
use App\Repository\ShortUrlRepository;
use App\Service\ShortLinkService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Uid\UuidV7;

class ShortLinkServiceTest extends TestCase
{
    private const UUID = '01962ee1-f9d5-7651-97b8-d0c6b1a8eb4b';
    private ShortLinkService $service;
    private $repositoryMock;
    private $busMock;
    private $lockFactoryMock;
    private $lockMock;
    private $factoryMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ShortUrlRepository::class);
        $this->busMock = $this->createMock(MessageBusInterface::class);
        $this->lockFactoryMock = $this->createMock(ShortLinkLockFactory::class);
        $this->lockMock = $this->createMock(LockInterface::class);
        $this->factoryMock = $this->createMock(ShortLinkFactory::class);

        $this->service = new ShortLinkService(
            $this->repositoryMock,
            $this->busMock,
            $this->lockFactoryMock,
            $this->factoryMock,
            'https://short.url'
        );

        $this->lockFactoryMock->method('create')
            ->willReturn($this->lockMock);
    }

    /**
     * @covers \App\Service\ShortLinkService
     */
    public function testProcessWhenLockNotAcquired()
    {
        $this->lockMock->expects($this->once())
            ->method('acquire')
            ->willReturn(false);

        $response = $this->service->process('https://example.com');

        $this->assertInstanceOf(CreateShortLinkResponse::class, $response);
        $this->assertEquals(StatusType::GENERATING, $response->status);
        $this->assertNull($response->shortUrl);
    }

    /**
     * @covers \App\Service\ShortLinkService
     */
    public function testProcessWhenUrlExistsAndReady()
    {
        $shortUrl = new ShortUrl();
        $shortUrl->setShortCode('abc123')
            ->setStatus(StatusType::READY);

        $this->lockMock->expects($this->once())
            ->method('acquire')
            ->willReturn(true);

        $this->repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['originalUrl' => 'https://example.com'])
            ->willReturn($shortUrl);

        $response = $this->service->process('https://example.com');

        $this->assertEquals(StatusType::READY, $response->status);
        $this->assertEquals('https://short.url/abc123', $response->shortUrl);
    }

    /**
     * @covers \App\Service\ShortLinkService
     */
    public function testProcessWhenUrlExistsButGenerating()
    {
        $shortUrl = new ShortUrl();
        $shortUrl->setStatus(StatusType::GENERATING);

        $this->lockMock->expects($this->once())
            ->method('acquire')
            ->willReturn(true);

        $this->repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['originalUrl' => 'https://example.com'])
            ->willReturn($shortUrl);

        $response = $this->service->process('https://example.com');

        $this->assertEquals(StatusType::GENERATING, $response->status);
        $this->assertNull($response->shortUrl);
    }

    /**
     * @covers \App\Service\ShortLinkService
     */
    public function testProcessWhenUrlDoesNotExist()
    {
        $this->lockMock->expects($this->once())
            ->method('acquire')
            ->willReturn(true);

        $this->repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['originalUrl' => 'https://example.com'])
            ->willReturn(null);

        $newShortUrl = new ShortUrl();
        $newShortUrl->setOriginalUrl('https://example.com')
            ->setStatus(StatusType::GENERATING)
            ->setId(UuidV7::fromString(self::UUID))
        ;

        $this->factoryMock->expects($this->once())
            ->method('create')
            ->with('https://example.com')
            ->willReturn($newShortUrl);

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->with($newShortUrl);

        $expectedMessage = new GenerateShortLinkMessage(self::UUID);

        $this->busMock->expects($this->once())
            ->method('dispatch')
            ->with($expectedMessage)
            ->willReturnCallback(function ($message) {
                return new Envelope($message);
            });

        $response = $this->service->process('https://example.com');

        $this->assertEquals(StatusType::GENERATING, $response->status);
        $this->assertNull($response->shortUrl);
    }

    /**
     * @covers \App\Service\ShortLinkService
     */
    public function testLockIsReleased()
    {
        $this->lockMock->expects($this->once())
            ->method('acquire')
            ->willReturn(true);

        $shortUrl = new ShortUrl();
        $shortUrl->setId(UuidV7::fromString(self::UUID));

        $this->repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->willReturn($shortUrl);

        $this->lockMock->expects($this->once())
            ->method('release');

        $this->service->process('https://example.com');
    }
}