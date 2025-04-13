<?php

namespace App\MessageHandler;

use App\Enum\StatusType;
use App\Message\GenerateShortLinkMessage;
use App\Repository\ShortUrlRepository;
use App\Service\ShortLinkGenerator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GenerateShortLinkHandler
{
    public function __construct(
        private ShortUrlRepository $repository,
        private ShortLinkGenerator $generator,
    ) {
    }

    public function __invoke(GenerateShortLinkMessage $message): void
    {
        $shortUrl = $this->repository->find($message->shortLinkId);
        if (!$shortUrl || $shortUrl->isReady()) {
            return;
        }

        do {
            $code = $this->generator->generateShortLink();
        } while ($this->repository->findOneBy([
            'shortCode' => $code,
        ]) !== null);
        $shortUrl
            ->setShortCode($code)
            ->setStatus(StatusType::READY);

        $this->repository->save($shortUrl);
    }
}
