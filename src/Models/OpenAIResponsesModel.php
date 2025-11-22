<?php

namespace JoBins\Agents\Models;

use JoBins\Agents\Providers\OpenAI\Client;

class OpenAIResponsesModel implements Model
{
    public function __construct(
        public string $model,
        public Client $client,
    ) {}

    public function getResponse()
    {
        $inputJson = json_encode(
            list_input,
            indent = 2,
            ensure_ascii = false,
        );

        $this->client->responses->create(
            model: $this->model,
            input = list_input,
        );
    }
}
