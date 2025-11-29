<?php

namespace JoBins\Agents\Schema\Parsers;

use Illuminate\JsonSchema\Types\ArrayType;
use JoBins\Agents\Schema\Attributes\ArrayOf;
use JoBins\Agents\Schema\Attributes\Field;
use JoBins\Agents\Schema\Schema;

class ArrayParser
{
    public ArrayType $type;

    public function __construct()
    {
        $this->type = new ArrayType();
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

    public function setArrayOf(ArrayOf $arrayOf): static
    {
        $itemType = Schema::makeType($arrayOf->type);

        $this->type->items($itemType);

        return $this;
    }
}
