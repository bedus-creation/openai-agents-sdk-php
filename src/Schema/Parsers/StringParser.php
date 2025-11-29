<?php

namespace JoBins\Agents\Schema\Parsers;

use Illuminate\JsonSchema\Types\StringType;
use JoBins\Agents\Schema\Attributes\Enum;
use JoBins\Agents\Schema\Attributes\Field;
use JoBins\Agents\Schema\Attributes\Format;
use JoBins\Agents\Schema\Attributes\Size;

class StringParser
{
    public StringType $type;

    public function __construct()
    {
        $this->type = new StringType();
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

    public function setSize(Size $size): static
    {
        if ($size->min) {
            $this->type->min($size->min);
        }

        if ($size->max) {
            $this->type->max($size->max);
        }

        return $this;
    }

    public function setFormat(Format $format): static
    {
        if ($format->format) {
            $this->type->format($format->format);
        }

        return $this;
    }

    public function setEnum(Enum $enum): static
    {
        $this->type->enum($enum->values);

        return $this;
    }
}
