<?php

namespace JoBins\Agents\Schema\Parsers;

use Illuminate\JsonSchema\Types\IntegerType;
use JoBins\Agents\Schema\Attributes\Enum;
use JoBins\Agents\Schema\Attributes\Field;

class IntegerParser
{
    public IntegerType $type;

    public function __construct()
    {
        $this->type = new IntegerType();
    }

    public function setField(Field $field): static
    {
        if ($field->name) {
            $this->type->title($field->name);
        }

        if ($field->description) {
            $this->type->description($field->description);
        };

        return $this;
    }

    public function setEnum(Enum $enum): static
    {
        $this->type->enum($enum->values);

        return $this;
    }
}
