<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Tools\Response;
use JoBins\Agents\Tools\Tool;

class ResumeParserTool extends Tool
{
    function schema(): string
    {
       return ResumeSchema::class;
    }

    function handle(ResumeSchema $schema): array|Response
    {

    }
}
