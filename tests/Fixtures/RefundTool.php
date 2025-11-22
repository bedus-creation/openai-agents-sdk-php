<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Tools\Response;

class RefundTool
{
    function schema(): string
    {
        return RefundSchema::class;
    }

    function handle(RefundSchema $schema): array|Response
    {

    }
}
