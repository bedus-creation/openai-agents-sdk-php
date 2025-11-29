<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Attributes\ArrayOf;
use JoBins\Agents\Schema\Attributes\Field;
use JoBins\Agents\Schema\Schema;

class ArraySchema extends Schema
{
    #[Field(description: "List of quantities")]
    #[ArrayOf('integer')]
    public array $quantities;

    #[Field(description: "Available colors")]
    #[ArrayOf(Color::class)]
    public array $colors;

    #[Field(description: "Saved addresses")]
    #[ArrayOf(AddressSchema::class)]
    public array $addresses;
}
