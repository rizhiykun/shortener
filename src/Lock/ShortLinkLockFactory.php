<?php

namespace App\Lock;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class ShortLinkLockFactory
{
    public const KEY = 'short_url_';

    public function __construct(
        private LockFactory $lockFactory,
        private float $ttl
    ) {
    }

    public function create(string $originalUrl): LockInterface
    {
        $key = self::KEY . hash('sha256', $originalUrl);

        return $this->lockFactory->createLock($key, $this->ttl);
    }
}
