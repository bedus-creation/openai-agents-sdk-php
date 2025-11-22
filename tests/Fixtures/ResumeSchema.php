<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Schema;
use JoBins\Agents\Attributes\Field;

class ResumeSchema extends Schema
{
    #[Field(description: "", format: 'email', minLength: 1, maxLength: 100)]
    public string $name;

    #[Field()]
    public ?int $count;
}
