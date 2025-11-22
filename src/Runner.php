<?php

namespace JoBins\Agents;

use JoBins\Agents\Memory\Session;
use JoBins\Agents\Models\OpenAIResponsesModel;

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

        $runner->runSingleTurn($agent);
    }

    private function runSingleTurn(
        Agent $agent,
    ) {
        $model = $this->getModel();
        $model->getResponse();
    }

    public function getModel()
    {
        return new OpenAIResponsesModel();
    }
}
