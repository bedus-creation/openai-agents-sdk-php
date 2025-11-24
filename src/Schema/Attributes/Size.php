<?php

namespace JoBins\Agents\Schema\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Size
{
    public function __construct(
        public ?int $min = null,
        public ?int $max = null,
    ) {}
}
