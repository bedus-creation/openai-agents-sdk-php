<?php

namespace JoBins\Agents\Providers\OpenAI;

use Illuminate\Http\Client\Factory as Http;
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
        $this->http = (new Http())
            ->baseUrl($this->url)
            ->withToken(Config::getApiKey());

        $this->responses = new Responses($this->http);
    }
}
