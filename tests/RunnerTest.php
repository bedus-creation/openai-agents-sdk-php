<?php

namespace JoBins\Agents\Test;

use JetBrains\PhpStorm\NoReturn;
use JoBins\Agents\Agents\Agent;
use JoBins\Agents\Providers\OpenAI\Config;
use JoBins\Agents\Runner;
use JoBins\Agents\Test\Fixtures\HttpFake;
use JoBins\Agents\Test\Fixtures\JoBinsAgent;
use PHPUnit\Framework\Attributes\Test;

class RunnerTest extends TestCase
{
    #[Test]
    public function itRuns()
    {
        HttpFake::fakeTextResponse();
        Config::useApiKey(env('OPENAI_API_KEY') ?: 'test-key');

        $agent = new Agent(
            name: "Assistant",
            instructions: "You are a helpful assistant"
        );

        $response = Runner::run(agent: $agent, input: "Hello!");
        $text = $response->outputText();

        $this->assertIsString($text);
        $this->assertSame("Hello!\n\nHow can I help you today?", $text);
    }

    #[Test]
    public function itRunsAgentWithTools()
    {
        HttpFake::fakePasswordUpdateToolCall();
        Config::useApiKey(env('OPENAI_API_KEY') ?: 'test-key');

        $agent = JoBinsAgent::create();

        $response = Runner::run(agent: $agent, input: "Hello! I want to change my password to `111222333cs` ");

        // Should have a function call to password_update_tool
        $calls = $response->functionToolCalls();
        $this->assertIsArray($calls);
        $this->assertNotEmpty($calls);
        $this->assertSame('password_update_tool', $calls[0]->name);

        $args = $calls[0]->argumentsAsArray();
        $this->assertIsArray($args);
        $this->assertSame('111222333cs', $args['password'] ?? null);
        $this->assertSame('111222333cs', $args['confirmPassword'] ?? null);
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
