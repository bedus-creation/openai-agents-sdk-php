<?php

namespace JoBins\Agents\Results;

class ToolCallOutputItem
{
    public function __construct(
        public ResponseFunctionToolCall $call
    ) {}
}
