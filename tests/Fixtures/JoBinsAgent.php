<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Agents\Agent;

class JoBinsAgent extends Agent
{
    public string $name = "JoBins Agent";

    public ?string $instructions = <<<'TEXT'
                       Handle all direct user communication. 
                       Call the relevant tools when specialized expertise is needed.
                       TEXT;

    public array $tools = [
        PasswordUpdateTool::class,
        UserCreateTool::class
    ];
}
