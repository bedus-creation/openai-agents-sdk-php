<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Attributes\Field;
use JoBins\Agents\Schema\Schema;

class AgeSchema extends Schema
{
    #[Field(name: "Age", description: "Age of the account")]
    public int $age;
}
