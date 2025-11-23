<?php

namespace JoBins\Agents\Test;

use JoBins\Agents\Agents\Agent;
use JoBins\Agents\Test\Fixtures\JoBinsAgent;
use JoBins\Agents\Test\Fixtures\UserCreateTool;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AgentTest extends TestCase
{
    #[Test]
    public function it_creates_an_agent()
    {
        $agent = new Agent(
            name: 'Assistant'
        );

        $this->assertInstanceOf(Agent::class, $agent);
        $this->assertEquals('Assistant', $agent->name);
    }

    #[Test]
    public function it_creates_an_agent_from_properties()
    {
        $agent = JoBinsAgent::create();

        $this->assertInstanceOf(Agent::class, $agent);
        $this->assertEquals('JoBins Agent', $agent->name);
    }

    #[Test]
    public function it_allows_to_set_tools()
    {
        $agent = new Agent(
            name: 'Assistant',
            tools: [UserCreateTool::class]
        );

        $this->assertInstanceOf(Agent::class, $agent);
    }
}
