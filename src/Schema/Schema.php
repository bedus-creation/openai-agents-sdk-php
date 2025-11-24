<?php

namespace JoBins\Agents\Schema;

use Illuminate\JsonSchema\Types\ObjectType;
use Illuminate\JsonSchema\Types\IntegerType;
use Illuminate\JsonSchema\Types\StringType;
use Illuminate\JsonSchema\Types\Type;
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

        $typeName = $type->getName();

        if (is_subclass_of($typeName, Schema::class)) {
            return $typeName::getProperties();
        }

        if (enum_exists($typeName)) {
            $cases = $typeName::cases();

            if (is_subclass_of($typeName, \BackedEnum::class)) {
                $values = array_map(fn (\BackedEnum $case) => $case->value, $cases);
                $typeInstance = is_string($values[0]) ? new StringType() : new IntegerType();
            } else {
                $values = array_map(fn (\UnitEnum $case) => $case->name, $cases);
                $typeInstance = new StringType();
            }

            $typeInstance->enum($values);

            return $typeInstance;
        }

        return match ($typeName) {
            'int' => 'integer',
            'bool' => 'boolean',
            'float' => 'number',
            default => 'string',
        };
    }
}
