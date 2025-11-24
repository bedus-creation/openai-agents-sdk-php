<?php

namespace JoBins\Agents\Schema\Parsers;

use Illuminate\JsonSchema\Types\Type;
use JoBins\Agents\Schema\Attributes\Field;

class TypePassthroughParser
{
    public function __construct(
        public Type $type
    ) {}

    public function setField(Field $field): static
    {
        if ($field->name) {
            $this->type->title($field->name);
        }

        if ($field->description) {
            $this->type->description($field->description);
        }

        return $this;
    }
}
