<?php

namespace JoBins\Agents\Tools;

use JoBins\Agents\Test\Fixtures\ResumeSchema;

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
     * @param T|ResumeSchema $schema
     *
     * @return array| Response
     */
    abstract function handle(ResumeSchema $schema): array|Response;
}
