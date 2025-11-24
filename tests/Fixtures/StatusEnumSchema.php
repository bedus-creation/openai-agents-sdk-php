<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Attributes\Enum;
use JoBins\Agents\Schema\Schema;

class StatusEnumSchema extends Schema
{
    #[Enum(['pending', 'active', 'disabled'])]
    public string $status;

    #[Enum([1, 2, 3])]
    public int $priority;
}
