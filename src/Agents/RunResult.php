<?php

namespace JoBins\Agents\Agents;

use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Arr;
use JoBins\Agents\Results\ResponseFunctionToolCall;
use JoBins\Agents\Results\ModelResponse;
use JoBins\Agents\Results\MessageOutputItem;
use JoBins\Agents\Results\ToolCallOutputItem;
use JoBins\Agents\Tools\Tool;

class RunResult
{
    /**
     * The originating agent for this run.
     */
    public Agent $lastAgent;

    /**
     * The raw HTTP response returned by the provider.
     */
    protected HttpResponse $httpResponse;

    /**
     * Decoded JSON payload of the response.
     */
    protected array $payload = [];

    /**
     * Map of tool name => Tool instance available to this run.
     * Populated from the Agent's tools list.
     *
     * @var array<string, Tool>
     */
    protected array $toolsByName = [];

    /**
     * Results produced by executing function tool calls (if any).
     * Each item contains: call (ResponseFunctionToolCall) and result (mixed)
     *
     * @var array<int, array{call: ResponseFunctionToolCall, result: mixed}>
     */
    protected array $executedToolResults = [];

    /**
     * Raw model responses collected during the run (single turn for now).
     * @var array<int, ModelResponse>
     */
    protected array $rawResponses = [];

    /**
     * Normalized list of output items: message text items and tool call items.
     * @var array<int, MessageOutputItem|ToolCallOutputItem>
     */
    protected array $newItems = [];

    public function __construct(Agent $agent, HttpResponse $httpResponse)
    {
        $this->lastAgent = $agent;
        $this->httpResponse = $httpResponse;
        $this->payload = $httpResponse->json() ?? [];

        // Build tool registry for easy lookup by function name
        $this->toolsByName = $this->buildToolRegistry($agent);

        // Capture raw response wrapper
        $this->rawResponses[] = new ModelResponse($httpResponse);

        // Precompute normalized output items
        $this->newItems = $this->buildNewItemsFromPayload($this->payload);
    }

    /**
     * Returns the last agent that produced this result.
     */
    public function lastAgent(): Agent
    {
        return $this->lastAgent;
    }

    /**
     * Get the full JSON payload returned by the provider.
     */
    public function json(): array
    {
        return $this->payload;
    }

    /**
     * Alias of json().
     */
    public function toArray(): array
    {
        return $this->json();
    }

    /**
     * Convenience: Return the first assistant text found in the output, if any.
     */
    public function outputText(): ?string
    {
        $output = $this->payload['output'] ?? [];
        foreach ($output as $item) {
            // New Responses API can provide a message with content blocks
            if (($item['type'] ?? null) === 'message') {
                foreach ($item['content'] ?? [] as $content) {
                    if (($content['type'] ?? null) === 'output_text' && isset($content['text'])) {
                        return $content['text'];
                    }
                }
            }
            // Some responses may directly include text
            if (($item['type'] ?? null) === 'output_text' && isset($item['text'])) {
                return $item['text'];
            }
        }

        return null;
    }

    /**
     * Extract function tool calls from the response output.
     *
     * @return array<int, ResponseFunctionToolCall>
     */
    public function functionToolCalls(): array
    {
        $calls = [];
        $output = $this->payload['output'] ?? [];
        foreach ($output as $item) {
            if (($item['type'] ?? null) === 'function_call') {
                $calls[] = new ResponseFunctionToolCall(
                    id: $item['id'] ?? null,
                    name: $item['name'] ?? null,
                    callId: $item['call_id'] ?? null,
                    status: $item['status'] ?? null,
                    arguments: $item['arguments'] ?? null,
                );
            }
        }

        return $calls;
    }

    /**
     * Return the underlying HTTP response object.
     */
    public function http(): HttpResponse
    {
        return $this->httpResponse;
    }

    /**
     * Returns normalized output items list.
     * @return array<int, MessageOutputItem|ToolCallOutputItem>
     */
    public function newItems(): array
    {
        return $this->newItems;
    }

    /**
     * snake_case alias for newItems().
     * @return array<int, MessageOutputItem|ToolCallOutputItem>
     */
    public function new_items(): array
    {
        return $this->newItems();
    }

    /**
     * Returns collected raw model responses.
     * @return array<int, ModelResponse>
     */
    public function rawResponses(): array
    {
        return $this->rawResponses;
    }

    /**
     * snake_case alias for rawResponses().
     * @return array<int, ModelResponse>
     */
    public function raw_responses(): array
    {
        return $this->rawResponses();
    }

    /**
     * Execute all function tool calls in the response (if any) and collect results.
     * Safe to call multiple times; execution will happen only once.
     *
     * @return array<int, array{call: ResponseFunctionToolCall, result: mixed}>
     */
    public function executeTools(): array
    {
        if (!empty($this->executedToolResults)) {
            return $this->executedToolResults;
        }

        foreach ($this->functionToolCalls() as $call) {
            $tool = $this->resolveToolByName($call->name ?? '');
            if (!$tool instanceof Tool) {
                // No matching tool; record as null result for visibility
                $this->executedToolResults[] = [
                    'call' => $call,
                    'result' => null,
                ];
                continue;
            }

            // Hydrate schema from arguments and invoke the handler
            $args = $call->argumentsAsArray() ?? [];
            $schemaClass = $tool->schema();
            $schema = $this->hydrateSchema($schemaClass, $args);

            $result = $tool->handle($schema);

            $this->executedToolResults[] = [
                'call' => $call,
                'result' => $result,
            ];
        }

        return $this->executedToolResults;
    }

    /**
     * Returns the results of executed tools without triggering execution.
     * If you want to ensure tools are executed, call executeTools() instead.
     *
     * @return array<int, array{call: ResponseFunctionToolCall, result: mixed}>
     */
    public function executedToolResults(): array
    {
        return $this->executedToolResults;
    }

    /**
     * Convenience accessor similar to OpenAI SDK's final_output:
     * - If the model returned function tool calls, returns an array containing
     *   the assistant text (if any) and the executed tool results.
     * - Otherwise, returns the assistant text (string|null).
     *
     * @return array|string|null
     */
    public function finalOutput(): mixed
    {
        // Per new spec: final_output is the last message (Any)
        $last = null;
        foreach ($this->newItems as $item) {
            if ($item instanceof MessageOutputItem) {
                $last = $item->text;
            }
        }
        return $last ?? $this->outputText();
    }

    /**
     * snake_case alias.
     *
     * @return array|string|null
     */
    public function final_output(): mixed
    {
        return $this->finalOutput();
    }

    /**
     * Build map of function name => Tool instance from the provided Agent.
     *
     * @return array<string, Tool>
     */
    protected function buildToolRegistry(Agent $agent): array
    {
        $tools = [];
        foreach ($agent->tools as $toolClass) {
            if (!is_string($toolClass)) {
                continue;
            }
            if (!class_exists($toolClass)) {
                continue;
            }
            $instance = new $toolClass();
            if (!$instance instanceof Tool) {
                continue;
            }

            $arr = $instance->toArray();
            $name = $arr['name'] ?? null;
            if (is_string($name) && $name !== '') {
                $tools[$name] = $instance;
            }
        }

        return $tools;
    }

    protected function resolveToolByName(string $name): ?Tool
    {
        return $this->toolsByName[$name] ?? null;
    }

    /**
     * Turn associative array of arguments into an instance of the tool's schema class.
     * The schema is a simple DTO with public properties.
     *
     * @template TSchema of object
     * @param class-string<TSchema> $schemaClass
     * @param array $args
     * @return TSchema
     */
    protected function hydrateSchema(string $schemaClass, array $args): object
    {
        $schema = new $schemaClass();
        foreach ($args as $key => $value) {
            // Only assign existing public properties to avoid typos
            if (property_exists($schema, $key)) {
                $schema->{$key} = $value;
            }
        }

        return $schema;
    }

    /**
     * Orchestrator hook: allow RunImpl to inject executed tool results.
     *
     * @param array<int, array{call: ResponseFunctionToolCall, result: mixed}> $results
     * @return void
     */
    public function setExecutedToolResults(array $results): void
    {
        $this->executedToolResults = $results;
    }

    /**
     * Build normalized items list (messages and tool calls) from payload.
     *
     * @param array $payload
     * @return array<int, MessageOutputItem|ToolCallOutputItem>
     */
    protected function buildNewItemsFromPayload(array $payload): array
    {
        $items = [];
        $output = $payload['output'] ?? [];
        foreach ($output as $node) {
            $type = $node['type'] ?? null;
            if ($type === 'message') {
                // Gather output_text blocks inside message
                foreach (($node['content'] ?? []) as $content) {
                    if (($content['type'] ?? null) === 'output_text' && isset($content['text'])) {
                        $items[] = new MessageOutputItem($content['text']);
                    }
                }
            } elseif ($type === 'output_text' && isset($node['text'])) {
                $items[] = new MessageOutputItem($node['text']);
            } elseif ($type === 'function_call') {
                $call = new ResponseFunctionToolCall(
                    id: $node['id'] ?? null,
                    name: $node['name'] ?? null,
                    callId: $node['call_id'] ?? null,
                    status: $node['status'] ?? null,
                    arguments: $node['arguments'] ?? null,
                );
                $items[] = new ToolCallOutputItem($call);
            }
        }
        return $items;
    }

    /**
     * Allow snake_case access like `$result->last_agent`.
     */
    public function __get(string $name)
    {
        if ($name === 'last_agent') {
            return $this->lastAgent();
        }

        return null;
    }
}
