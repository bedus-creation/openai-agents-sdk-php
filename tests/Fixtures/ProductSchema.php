<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Schema\Attributes\Field;
use JoBins\Agents\Schema\Schema;

class ProductSchema extends Schema
{
    #[Field(description: "Selected color")]
    public Color $color;
}
