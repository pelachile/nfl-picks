<?php

namespace App\DataObjects;

class TeamData
{
    public function __construct(
        public string $name,
        public string $abbreviation,
        public string $displayName,
        public ?string $color = null,
        public ?string $logo = null,
        public array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'display_name' => $this->displayName,
            'color' => $this->color,
            'logo' => $this->logo,
            'metadata' => $this->metadata,
        ];
    }
}
