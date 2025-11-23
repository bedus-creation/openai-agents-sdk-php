<?php

namespace JoBins\Agents\Schema\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Format
{
    public function __construct(
        public ?string $format = null,
    ) {}
}
