<?php

namespace JoBins\Agents;

abstract class Tool
{
    public function shouldRegister(Request $request): bool
    {
        return true;
    }

    abstract function schema(): Schema;

    abstract function handle(Schema $schema): mixed;
}
