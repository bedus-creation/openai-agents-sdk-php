<?php

namespace JoBins\Agents\Tools;

use JoBins\Agents\Schema\Schema;
use JoBins\Agents\Schema\SchemaInterface;

/**
 * @template T of SchemaInterface
 */
abstract class Tool
{
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
}
