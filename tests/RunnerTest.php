<?php

namespace JoBins\Agents\Test;

use JoBins\Agents\Agents\Agent;
use JoBins\Agents\Providers\OpenAI\Config;
use JoBins\Agents\Runner;
use JoBins\Agents\Test\Fixtures\Fixtures\FetchWeather;
use PHPUnit\Framework\Attributes\Test;

class RunnerTest extends TestCase
{
    #[Test]
    public function itRuns()
    {
        Config::useApiKey(env('OPENAI_API_KEY'));

        $agent = new Agent(
            name: "Assistant",
            instructions: "You are a helpful assistant"
        );

        Runner::run(agent: $agent, input: "Hello!");
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
