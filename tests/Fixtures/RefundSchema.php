<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Attributes\Field;
use JoBins\Agents\Schema\Schema;

class RefundSchema extends Schema
{
    #[Field(description: "The name of the person to refund.", format: 'email', minLength: 1, maxLength: 100)]
    public string $name;

    #[Field(description: "The number of items to refund.")]
    public ?int $count;
}
