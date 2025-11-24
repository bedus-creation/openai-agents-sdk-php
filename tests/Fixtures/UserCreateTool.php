<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Tools\Response;
use JoBins\Agents\Tools\Tool;

class UserCreateTool extends Tool
{
    function schema(): string
    {
        return UserCreateSchema::class;
    }

    /**
     * @param UserCreateSchema $schema
     */
    function handle($schema): array|Response {}
}
