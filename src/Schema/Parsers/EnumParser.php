<?php

namespace JoBins\Agents\Schema\Parsers;

use BackedEnum;
use Illuminate\JsonSchema\Types\IntegerType;
use Illuminate\JsonSchema\Types\StringType;
use Illuminate\JsonSchema\Types\Type;
use UnitEnum;

class EnumParser
{
    /**
     * @param class-string<BackedEnum|UnitEnum> $enumClass
     *
     * @return Type
     */
    public static function parse(string $enumClass): Type
    {
        $cases = $enumClass::cases();

        if (is_subclass_of($enumClass, BackedEnum::class)) {
            $values       = array_map(fn(BackedEnum $case) => $case->value, $cases);
            $typeInstance = is_string($values[0]) ? new StringType() : new IntegerType();
        } else {
            $values       = array_map(fn(UnitEnum $case) => $case->name, $cases);
            $typeInstance = new StringType();
        }

        $typeInstance->enum($values);

        return $typeInstance;
    }
}
