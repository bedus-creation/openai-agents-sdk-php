<?php

namespace JoBins\Agents\Schema\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Enum
{
    /**
     * @param array<int, string|int> $values
     */
    public function __construct(
        public array $values
    ) {}
}
