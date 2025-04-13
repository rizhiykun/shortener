<?php

namespace App\Entity;

use App\Enum\StatusType;
use App\Repository\ShortUrlRepository;
use App\Traits\UpdateTimestampsTrait;
use App\Traits\UuidIdTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShortUrlRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_original_url', columns: ['original_url'])]
#[ORM\UniqueConstraint(name: 'uniq_short_code', columns: ['short_code'])]
#[ORM\Index(name: 'idx_original_url', columns: ['original_url'])]
#[ORM\Index(name: 'idx_short_code', columns: ['short_code'])]
#[ORM\Index(name: 'idx_status', columns: ['status'])]
#[ORM\HasLifecycleCallbacks]
class ShortUrl
{
    use UuidIdTrait, UpdateTimestampsTrait;

    #[ORM\Column(name: 'original_url', type: 'string', length: 2048)]
    private string $originalUrl;

    #[ORM\Column(name: 'short_code', type: 'string', length: 8, unique: true, nullable: true)]
    private ?string $shortCode = null;

    #[ORM\Column(name: 'status', type: 'string', length: 16, nullable: true)]
    private string $status = StatusType::GENERATING;

    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }

    public function setOriginalUrl(string $originalUrl): static
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }

    public function getShortCode(): ?string
    {
        return $this->shortCode;
    }

    public function setShortCode(?string $shortCode): static
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function isReady(): bool
    {
        return $this->status === StatusType::READY;
    }
}
