<?php

namespace JoBins\Agents\Test;

class Schema
{
    public function __construct(
        public string $name,
        public int|null $count
    ) {}
}

class SchemaString
{
    public function __construct(
        #[StringSchema(format: 'email', minLength: 1)]
        public string $name
    ) {}
}
