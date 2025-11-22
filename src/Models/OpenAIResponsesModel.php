<?php

namespace JoBins\Agents\Models;

use JoBins\Agents\Providers\OpenAI\Client;

class OpenAIResponsesModel implements Model
{
    public function __construct(
        public string $model,
        public Client $client,
    ) {}

    public function getResponse(
        string|null $instructions,
        array|string $input,
        ModelSettings $modelSettings,
        array $tools,
    ) {
        return $this->client->responses->create(
            model: $this->model,
            instructions: $instructions,
            input: $input,
            tools: $tools,
            temperature: $modelSettings->temperature,
        );
    }
}
