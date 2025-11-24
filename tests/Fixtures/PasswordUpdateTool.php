<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Tools\Response;
use JoBins\Agents\Tools\Tool;

class PasswordUpdateTool extends Tool
{
    function schema(): string
    {
        return PasswordUpdateSchema::class;
    }

    /**
     * @param PasswordUpdateSchema $schema
     */
    function handle($schema): array|Response
    {
        return [];
    }
}
