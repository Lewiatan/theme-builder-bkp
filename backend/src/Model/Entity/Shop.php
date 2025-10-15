<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\ValueObject\ThemeSettings;
use DateTimeImmutable;
use InvalidArgumentException;

final class Shop
{
    private const int MAX_NAME_LENGTH = 60;

    public function __construct(
        private string $id,
        private string $userId,
        private string $name,
        private ThemeSettings $themeSettings,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
        $this->validateName($name);
    }

    public static function create(
        string $id,
        string $userId,
        string $name
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            $id,
            $userId,
            $name,
            ThemeSettings::default(),
            $now,
            $now
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getThemeSettings(): ThemeSettings
    {
        return $this->themeSettings;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->validateName($name);
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateThemeSettings(ThemeSettings $themeSettings): void
    {
        $this->themeSettings = $themeSettings;
        $this->updatedAt = new DateTimeImmutable();
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("Shop name cannot be empty");
        }

        if (mb_strlen($name) > self::MAX_NAME_LENGTH) {
            throw new InvalidArgumentException(
                sprintf("Shop name cannot exceed %d characters", self::MAX_NAME_LENGTH)
            );
        }
    }
}
