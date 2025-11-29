<?php

namespace JoBins\Agents\Schema;

use JoBins\Agents\Schema\Parsers\IntegerParser;
use JoBins\Agents\Schema\Parsers\TypePassthroughParser;
use JoBins\Agents\Schema\Parsers\StringParser;
use JoBins\Agents\Schema\Parsers\ArrayParser;
use Illuminate\JsonSchema\Types\Type;

class AttributerParser
{
    public function __construct(
        public string|Type $type,
        public array $attributes
    ) {}

    public function parse()
    {
        $parser = $this->type instanceof Type
            ? new TypePassthroughParser($this->type)
            : match ($this->type) {
                'string' => new StringParser(),
                'integer' => new IntegerParser(),
                'array' => new ArrayParser(),
                default => new TypePassthroughParser(Schema::makeType($this->type)),
            };

        foreach ($this->attributes as $attribute) {
            $attribute->newInstance();

            $instance = $attribute->newInstance();
            $method   = 'set'.class_basename($attribute->getName());
            $parser->$method($instance);
        }

        return $parser->type;
    }
}
