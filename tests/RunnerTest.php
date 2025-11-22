<?php

namespace JoBins\Agents\Test;

use JoBins\Agents\Agent;
use JoBins\Agents\Runner;
use JoBins\Agents\Test\Fixtures\FetchWeather;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RunnerTest extends TestCase
{
    #[Test]
    public function itRuns()
    {
        $agent = new Agent(
            name: "Assistant",
            instructions: "You are a helpful assistant"
        );

        $results = Runner::run(agent: $agent, input: "Hello world!");
        print_r($results);
    }

    public function itRunsWithMemory()
    {
        $agent = new Agent(
            name: "Assistant",
            instructions: "You are a helpful assistant"
        );

        $memory = Memory::create('conversation_123');

        Runner::run(
            $agent,
            "What city is the Golden Gate Bridge in?",
            $memory
        );
    }

    public function itRunsWithTools()
    {
        $agent = new Agent(
            name: "Assistant",
            tools: [
                FetchWeather::class
            ]
        );

        $memory = Memory::create('conversation_123');

        Runner::run(
            $agent,
            "What city is the Golden Gate Bridge in?",
            $memory
        );
    }

}
