<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Tools\Response;
use JoBins\Agents\Tools\Tool;

class BookingTool extends Tool
{
    function schema(): string
    {
       return RefundSchema::class;
    }

    function handle(RefundSchema $schema): array|Response
    {

    }
}
