<?php

namespace App\Service;

use App\DTO\Responses\CreateShortLinkResponse;
use App\Enum\StatusType;
use App\Factory\ShortLinkFactory;
use App\Lock\ShortLinkLockFactory;
use App\Message\GenerateShortLinkMessage;
use App\Repository\ShortUrlRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class ShortLinkService extends BaseService
{
    public function __construct(
        private readonly ShortUrlRepository   $repository,
        private readonly MessageBusInterface  $bus,
        private readonly ShortLinkLockFactory $lockFactory,
        private readonly ShortLinkFactory     $shortLinkFactory,
        private readonly string               $baseUrl
    ) {
    }

    public function process(string $originalUrl): CreateShortLinkResponse
    {
        $lock = $this->lockFactory->create($originalUrl);

        if (!$lock->acquire()) {
            return new CreateShortLinkResponse(StatusType::GENERATING);
        }

        try {
            $shortUrl = $this->repository->findOneBy([
                'originalUrl' => $originalUrl,
            ]);

            if ($shortUrl && $shortUrl->getStatus() === StatusType::READY) {
                return new CreateShortLinkResponse(StatusType::READY, $this->baseUrl . $shortUrl->getShortCode());
            }

            if (!$shortUrl) {
                $shortUrl = $this->shortLinkFactory->create($originalUrl);
                $this->repository->save($shortUrl);

                $this->bus->dispatch(new GenerateShortLinkMessage($shortUrl->getId()));
            }

            return new CreateShortLinkResponse(StatusType::GENERATING);
        } finally {
            $lock->release();
        }
    }
}
