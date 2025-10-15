<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Enum\PageType;
use App\Model\ValueObject\Layout;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pages')]
#[ORM\UniqueConstraint(name: 'idx_pages_shop_type', columns: ['shop_id', 'type'])]
final class Page
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'guid', unique: true)]
        private string $id,

        #[ORM\Column(name: 'shop_id', type: 'guid', nullable: false)]
        private string $shopId,

        #[ORM\Column(type: 'string', enumType: PageType::class, nullable: false)]
        private PageType $type,

        #[ORM\Embedded(class: Layout::class, columnPrefix: false)]
        private Layout $layout,

        #[ORM\Column(name: 'created_at', type: 'datetime_immutable', nullable: false)]
        private DateTimeImmutable $createdAt,

        #[ORM\Column(name: 'updated_at', type: 'datetime_immutable', nullable: false)]
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
