<?php

namespace JoBins\Agents\Test\Fixtures;

use Illuminate\Http\Client\Factory;
use JoBins\Agents\Providers\OpenAI\Config;

class HttpFake
{
    public static function fake(array $response): void
    {
        $http = new Factory();
        $http->fake(['https://api.openai.com/*' => $response])->preventStrayRequests();

        Config::setHttp($http);
    }

    public static function fakePasswordUpdateToolCall(): void
    {
        $fixture = json_decode(file_get_contents(__DIR__.'/Responses/password_update_tool.json'), true);

        self::fake($fixture);
    }

    public static function fakeTextResponse(): void
    {
        $fixture = json_decode(file_get_contents(__DIR__.'/Responses/text_response.json'), true);

        self::fake($fixture);
    }
}
