<?php

namespace JoBins\Agents\Models;

interface Model
{
    public function getResponse(
        string|null $instructions,
        array|string $input,
        ModelSettings $modelSettings,
        array $tools
    );
}
