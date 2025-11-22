<?php

namespace JoBins\Agents\Test\Fixtures;

use JoBins\Agents\Agents\Agent;

class CustomerFacingAgent extends Agent
{
    public static function create(): CustomerFacingAgent
    {
        return new self(
            name: "Customer Facing Agent",
            instructions: <<<'TEXT'
                Handle all direct user communication. 
                Call the relevant tools when specialized expertise is needed.
                TEXT,
            tools: [
                BookingTool::class,
                RefundSchema::class
            ]
        );
    }
}
