<?php

namespace JoBins\Agents\Models;

class ModelSettings
{
    public function __construct(
        public int|null $temperature = null
    ) {}
}
