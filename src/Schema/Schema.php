<?php

namespace JoBins\Agents\Schema;

use JoBins\Agents\Attributes\Field;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

class Schema
{
    public static function toJsonSchema(): array
    {
        $class = new ReflectionClass(static::class);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        $schema = [
            'type' => 'object',
            'properties' => [],
            'required' => [],
        ];

        foreach ($properties as $property) {
            $type = self::getPropertyType($property);
            $attributes = $property->getAttributes(Field::class);

            $fieldSchema = ['type' => $type];

            if (! empty($attributes)) {
                $fieldAttribute = $attributes[0]->newInstance();
                if ($fieldAttribute->description) {
                    $fieldSchema['description'] = $fieldAttribute->description;
                }
                if ($fieldAttribute->format) {
                    $fieldSchema['format'] = $fieldAttribute->format;
                }
                if ($fieldAttribute->minLength) {
                    $fieldSchema['minLength'] = $fieldAttribute->minLength;
                }
                if ($fieldAttribute->maxLength) {
                    $fieldSchema['maxLength'] = $fieldAttribute->maxLength;
                }
            }

            $schema['properties'][$property->getName()] = $fieldSchema;

            if (! $property->getType()->allowsNull()) {
                $schema['required'][] = $property->getName();
            }
        }

        return $schema;
    }

    protected static function getPropertyType(ReflectionProperty $property): string
    {
        $type = $property->getType();

        if (! $type instanceof ReflectionNamedType) {
            return 'string';
        }

        return match ($type->getName()) {
            'int' => 'integer',
            'bool' => 'boolean',
            'float' => 'number',
            default => 'string',
        };
    }
}
