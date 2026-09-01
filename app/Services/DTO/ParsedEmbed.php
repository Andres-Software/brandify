<?php

namespace App\Services\DTO;

readonly class ParsedEmbed
{
    public function __construct(
        public ?string $src,
        public ?string $title,
    ) {}
}
