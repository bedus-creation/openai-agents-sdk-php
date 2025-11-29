<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Attributes\Field;
use JoBins\Agents\Schema\Schema;

class UserProfileSchema extends Schema
{
    #[Field(description: "Mailing address for the user")]
    public AddressSchema $address;

    #[Field(description: "Personal website URL")]
    public ?string $website;

    public ArraySchema $users;
}
