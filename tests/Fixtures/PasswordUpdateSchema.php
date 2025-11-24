<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Attributes\Size;
use JoBins\Agents\Schema\Schema;

class PasswordUpdateSchema extends Schema
{
    #[Size(min: 8)]
    public string $password;

    public string $confirmPassword;
}
