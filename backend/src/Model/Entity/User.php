<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\ValueObject\Email;
use DateTimeImmutable;

final class User
{
    public function __construct(
        private string $id,
        private Email $email,
        private string $password,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {}

    public static function create(
        string $id,
        Email $email,
        string $hashedPassword
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            $id,
            $email,
            $hashedPassword,
            $now,
            $now
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function changePassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateEmail(Email $email): void
    {
        $this->email = $email;
        $this->updatedAt = new DateTimeImmutable();
    }
}
