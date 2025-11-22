<?php

namespace JoBins\Agents\Providers\OpenAI\Endpoints;

use Illuminate\Http\Client\PendingRequest as Http;

class Responses
{
    public function __construct(protected Http $http) {}

    public function create(
        string $model,
        string $instructions = null,
        array $input = [],
        array $tools = [],
        string $previousResponseId = null,
        string $temperature = null
    ) {
        return $this->http->post('/v1/responses', [
            'model'                => $model,
            'instructions'         => $instructions,
            'input'                => $input,
            'tools'                => $tools,
            'temperature'          => $temperature,
            'previous_response_id' => $previousResponseId
        ]);
    }
}
