<?php

namespace JoBins\Agents\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    public function __construct(
        public ?string $description = null,
        public ?string $format = null,
        public ?int $minLength = null,
        public ?int $maxLength = null,
    ) {
    }
}
