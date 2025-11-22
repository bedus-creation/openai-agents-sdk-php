<?php

namespace JoBins\Agents;

use JoBins\Agents\Agents\Agent;
use JoBins\Agents\Helpers\InputHelper;
use JoBins\Agents\Memory\Session;
use JoBins\Agents\Providers\OpenAI\Config;

class Runner
{
    public function __construct(
        public Agent $agent,
        public string|array $input,
        public string|null $conversationId = null,
        public ?Session $session = null
    ) {}

    public static function run(
        Agent $agent,
        string|array $input,
        string|null $conversationId = null,
        ?Session $session = null
    ) {
        $runner = new self($agent, $input, $conversationId, $session);

        return $runner->runSingleTurn($agent, $input);
    }

    private function runSingleTurn(
        Agent $agent,
        string|array $originalInput,
    ) {
        $input = InputHelper::inputList($originalInput);

        return $this->getModel()->getResponse(
            instructions: $agent->instructions,
            input: $input,
            modelSettings: $agent->modelSettings,
            tools: $agent->tools
        );
    }

    public function getModel(): Models\Model
    {
        return Config::getModel($this->agent->model);
    }
}
