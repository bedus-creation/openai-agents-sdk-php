<?php

namespace JoBins\Agents\Agents;

use JoBins\Agents\Models\ModelSettings;
use JoBins\Agents\Providers\OpenAI\Config;

class Agent
{
    public function __construct(
        public string $name,
        public string|null $instructions = null,
        public string|null $model = null,
        public ModelSettings|null $modelSettings = null,
        public array $tools = [],
    ) {
        if (is_null($this->model)) {
            $this->model = Config::getDefaultModel();
        }

        if($this->modelSettings === null){
            $this->modelSettings = new ModelSettings();
        }
    }
}
