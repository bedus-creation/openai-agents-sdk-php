<?php

namespace JoBins\Agents\Tools;

use JoBins\Agents\Test\Fixtures\RefundSchema;

/**
 * @template T extends Schema
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
     * @param T|RefundSchema $schema
     *
     * @return array| Response
     */
    abstract function handle(RefundSchema $schema): array|Response;
}
