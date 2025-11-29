<?php

namespace JoBins\Agents\Schema;

use Illuminate\JsonSchema\Types\ArrayType;
use Illuminate\JsonSchema\Types\BooleanType;
use Illuminate\JsonSchema\Types\IntegerType;
use Illuminate\JsonSchema\Types\ObjectType;
use Illuminate\JsonSchema\Types\NumberType;
use Illuminate\JsonSchema\Types\StringType;
use Illuminate\JsonSchema\Types\Type;
use JoBins\Agents\Schema\Parsers\EnumParser;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

class Schema implements SchemaInterface
{
    public static function getProperties(): Type
    {
        $class      = new ReflectionClass(static::class);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        $propertiesTypes = [];
        $required = [];
        foreach ($properties as $property) {
            $type       = self::getPropertyType($property);
            $attributes = $property->getAttributes();

            $propertiesTypes[$property->getName()] = (new AttributerParser($type, $attributes))->parse();

            // Determine required: non-nullable properties are required
            $refType = $property->getType();
            if ($refType instanceof ReflectionNamedType && !$refType->allowsNull()) {
                $required[] = $property->getName();
            }
        }

        $object = ObjectType::object($propertiesTypes);
        // The illuminate/json-schema library models `required` on a per-property basis.
        // Mark each non-nullable property as required on its Type instance.
        if (!empty($required)) {
            foreach ($required as $propName) {
                if (isset($propertiesTypes[$propName]) && $propertiesTypes[$propName] instanceof Type) {
                    $propertiesTypes[$propName]->required(true);
                }
            }
        }

        return $object;
    }

    protected static function getPropertyType(ReflectionProperty $property): string|Type
    {
        $type = $property->getType();

        if (!$type instanceof ReflectionNamedType) {
            return 'string';
        }

        $typeName = self::normalizeTypeName($type->getName());

        if ($typeName === 'array') {
            return 'array';
        }

        return self::resolveType($typeName);
    }

    protected static function resolveType(string $typeName): string|Type
    {
        if (is_subclass_of($typeName, Schema::class)) {
            return $typeName::getProperties();
        }

        if (enum_exists($typeName)) {
            return EnumParser::parse($typeName);
        }

        return match ($typeName) {
            'integer', 'boolean', 'number', 'string' => $typeName,
            default => 'string',
        };
    }

    /**
     * Normalize primitive aliases to json-schema names.
     */
    protected static function normalizeTypeName(string $typeName): string
    {
        return match ($typeName) {
            'int' => 'integer',
            'bool' => 'boolean',
            'float', 'double' => 'number',
            default => $typeName,
        };
    }

    /**
     * Convert a type name or class string to a JsonSchema Type instance.
     */
    public static function makeType(string $typeName): Type
    {
        $typeName = self::normalizeTypeName($typeName);
        $resolved = self::resolveType($typeName);

        if ($resolved instanceof Type) {
            return $resolved;
        }

        return match ($resolved) {
            'integer' => new IntegerType(),
            'boolean' => new BooleanType(),
            'number' => new NumberType(),
            'array' => new ArrayType(),
            default => new StringType(),
        };
    }
}
