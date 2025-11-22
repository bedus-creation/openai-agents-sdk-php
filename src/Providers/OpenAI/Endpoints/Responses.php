<?php

namespace JoBins\Agents\Providers\OpenAI\Endpoints;

class Responses
{
    public function __construct(protected Http $http) {}

    public function create(
        string $model,
    ) {
        return $this->http->post('/v1/responses', [
            'model' => $model,
        ]);
    }
}
