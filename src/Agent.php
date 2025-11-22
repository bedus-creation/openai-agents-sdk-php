<?php

namespace JoBins\Agents;

use JoBins\Agents\Models\ModelSettings;

class Agent
{
    public function __construct(
        public string $name,
        public string|null $instructions = null,
        public string|null $model = null,
        public array|ModelSettings $modelSettings = [],
        public array $tools = [],
    ) {}
}
