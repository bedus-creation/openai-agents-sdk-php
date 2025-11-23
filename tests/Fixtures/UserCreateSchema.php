<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Attributes\Field;
use JoBins\Agents\Schema\Attributes\Format;
use JoBins\Agents\Schema\Attributes\Size;
use JoBins\Agents\Schema\Schema;

class UserCreateSchema extends Schema
{
    #[Field(description: "Fullname of the user")]
    #[Size(min: 1, max: 50)]
    public string $name;

    #[Field(description: "Email of the person")]
    #[Format('email')]
    public ?int $email;
}
