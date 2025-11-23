<?php

namespace JoBins\Agents\Schema\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    public function __construct(
        public ?string $description = null,
    ) {
    }
}
