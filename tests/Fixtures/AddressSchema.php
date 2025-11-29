<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Attributes\Enum;
use JoBins\Agents\Schema\Attributes\Field;
use JoBins\Agents\Schema\Schema;

class AddressSchema extends Schema
{
    #[Field(description: "Street address")]
    public string $street;

    #[Field(description: "City name")]
    public string $city;

    #[Field(description: "Country code")]
    #[Enum(['US', 'CA'])]
    public string $country;

    #[Field(description: "Color")]
    public Color $color;
}
