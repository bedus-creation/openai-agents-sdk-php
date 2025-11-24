<?php

namespace JoBins\Agents\Providers\OpenAI;

use Illuminate\Http\Client\Factory as HttpFactory;
use JoBins\Agents\Providers\OpenAI\Endpoints\ResponseHandler;
use JoBins\Agents\Providers\OpenAI\Endpoints\Responses;

class Client
{
    use ResponseHandler;

    protected $http;

    public function __construct(
        public string $url,
        public string|null $apiKey = null,
    ) {
        // Use a shared Http Factory from Config so tests can inject fakes
        $factory = Config::getHttp();
        if (!$factory instanceof HttpFactory) {
            $factory = new HttpFactory();
        }

        $this->http = $factory
            ->baseUrl($this->url)
            ->withToken(Config::getApiKey());

        $this->responses = new Responses($this->http);
    }
}
