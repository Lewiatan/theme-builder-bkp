<?php

declare(strict_types=1);

namespace App\Model\ValueObject;

use InvalidArgumentException;

final readonly class ComponentDefinition
{
    /**
     * @param array<int,mixed> $settings
     */
    public function __construct(
        private string $id,
        private string $type,
        private string $variant,
        private array $settings
    ) {
        $this->validate();
    }

    /**
     * @param array<int,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['id'], $data['type'], $data['variant'], $data['settings'])) {
            throw new InvalidArgumentException("Invalid component definition structure");
        }

        return new self(
            $data['id'],
            $data['type'],
            $data['variant'],
            $data['settings']
        );
    }
    /**
     * @return array<string,mixed>
     */
     public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'variant' => $this->variant,
            'settings' => $this->settings,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVariant(): string
    {
        return $this->variant;
    }

    /**
     * @return array<int,mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    private function validate(): void
    {
        if (empty($this->id)) {
            throw new InvalidArgumentException("Component ID cannot be empty");
        }

        if (empty($this->type)) {
            throw new InvalidArgumentException("Component type cannot be empty");
        }

        if (empty($this->variant)) {
            throw new InvalidArgumentException("Component variant cannot be empty");
        }
    }
}
