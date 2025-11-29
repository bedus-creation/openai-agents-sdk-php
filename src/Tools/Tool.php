<?php

namespace JoBins\Agents\Tools;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JoBins\Agents\Schema\Schema;
use JoBins\Agents\Schema\SchemaInterface;
use Illuminate\JsonSchema\Serializer;

/**
 * @template T of SchemaInterface
 */
abstract class Tool implements Arrayable
{
    public ?string $name = null;

    public ?string $description = null;

    public function shouldRegister(): bool
    {
        return true;
    }

    /**
     * @return class-string<T>
     */
    abstract function schema(): string;

    /**
     * @param T $schema
     *
     * @return array| Response
     */
    abstract function handle($schema): array|Response;

    public function toArray(): array
    {
        /** @var Schema $class */
        $class = $this->schema();

        return [
            "type"        => "function",
            "name"        => $this->name ??  Str::snake(class_basename(static::class)),
            "description" => $this->description,
            "parameters"  => Serializer::serialize($class::getProperties())
        ];
    }
}
