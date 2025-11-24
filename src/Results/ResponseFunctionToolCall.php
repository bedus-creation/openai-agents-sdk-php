<?php

namespace JoBins\Agents\Results;

class ResponseFunctionToolCall
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $callId = null,
        public ?string $status = null,
        public string|array|null $arguments = null,
    ) {}

    /**
     * Returns decoded arguments as array if provided as JSON string.
     */
    public function argumentsAsArray(): ?array
    {
        if (is_array($this->arguments)) {
            return $this->arguments;
        }

        if (is_string($this->arguments)) {
            $decoded = json_decode($this->arguments, true);
            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'call_id' => $this->callId,
            'status' => $this->status,
            'arguments' => $this->arguments,
        ];
    }
}
