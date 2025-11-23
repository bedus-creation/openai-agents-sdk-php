<?php

namespace JoBins\Agents\Agents;

use JoBins\Agents\Models\ModelSettings;
use JoBins\Agents\Providers\OpenAI\Config;

class Agent
{
    public string $name;

    public string|null $instructions = null;

    public array $tools = [];

    public function __construct(
        ?string $name = null,
        ?string $instructions = null,
        ?array $tools = null,
        public string|null $model = null,
        public ModelSettings|null $modelSettings = null,
    ) {
        if (!is_null($name)) {
            $this->name = $name;
        }

        if (!is_null($instructions)) {
            $this->instructions = $instructions;
        }

        if (!is_null($tools)) {
            $this->tools = $tools;
        }

        if (is_null($this->model)) {
            $this->model = Config::getDefaultModel();
        }

        if ($this->modelSettings === null) {
            $this->modelSettings = new ModelSettings();
        }
    }

    public static function create(): static
    {
        return new static();
    }
}
