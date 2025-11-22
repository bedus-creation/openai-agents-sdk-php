<?php

namespace JoBins\Agents\Test;

use JoBins\Agents\Agent;
use JoBins\Agents\Test\Fixtures\FetchWeather;
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
    }

    #[Test]
    public function it_allows_to_set_tools()
    {
        $agent = new Agent(
            name: 'Assistant',
            tools: [FetchWeather::class]
        );

        $this->assertInstanceOf(Agent::class, $agent);
    }
}
