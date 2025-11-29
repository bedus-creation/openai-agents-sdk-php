<?php

namespace JoBins\Agents\Results;

use Illuminate\Http\Client\Response as HttpResponse;

class ModelResponse
{
    public function __construct(
        public HttpResponse $http,
    ) {}

    public function status(): int
    {
        return $this->http->status();
    }

    public function headers(): array
    {
        return $this->http->headers();
    }

    public function json(): array
    {
        return $this->http->json() ?? [];
    }
}
