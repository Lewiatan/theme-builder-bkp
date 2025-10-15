<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Enum\PageType;
use App\Model\ValueObject\Layout;
use DateTimeImmutable;

final class Page
{
    public function __construct(
        private string $id,
        private string $shopId,
        private PageType $type,
        private Layout $layout,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {}

    public static function create(
        string $id,
        string $shopId,
        PageType $type,
        Layout $layout
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            $id,
            $shopId,
            $type,
            $layout,
            $now,
            $now
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getShopId(): string
    {
        return $this->shopId;
    }

    public function getType(): PageType
    {
        return $this->type;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateLayout(Layout $layout): void
    {
        $this->layout = $layout;
        $this->updatedAt = new DateTimeImmutable();
    }
}
