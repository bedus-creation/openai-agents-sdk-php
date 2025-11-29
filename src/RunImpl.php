<?php

namespace JoBins\Agents;

use JoBins\Agents\Agents\Agent;
use JoBins\Agents\Agents\RunResult;
use JoBins\Agents\Helpers\InputHelper;
use JoBins\Agents\Providers\OpenAI\Config;
use JoBins\Agents\Results\ResponseFunctionToolCall;
use JoBins\Agents\Tools\Tool;

/**
 * Basic single-turn run orchestrator inspired by OpenAI Agents RunImpl.
 *
 * This minimal implementation focuses on:
 * - Preparing the input list
 * - Instantiating tool classes declared on the Agent
 * - Asking the configured Model for a response
 * - Wrapping the HTTP response in a RunResult which can execute tools
 * - Returning the RunResult to the caller
 *
 * Advanced concerns like guardrails, streaming, shell/computer tools, or
 * multi-step/handoffs are intentionally out of scope for now.
 */
class RunImpl
{
    /**
     * Execute a single turn for the provided Agent and input, returning a RunResult.
     */
    public static function runSingleTurn(Agent $agent, string|array $originalInput): RunResult
    {
        // Normalize input into the Responses API expected array form
        $input = InputHelper::inputList($originalInput);

        // Instantiate declared tools
        $toolInstances = collect($agent->tools)
            ->filter(fn($tool) => is_string($tool) && class_exists($tool))
            ->map(fn($tool) => new $tool)
            ->filter(fn($instance) => $instance instanceof Tool)
            ->values()
            ->toArray();

        // Build map: tool name => Tool instance
        $toolsByName = [];
        foreach ($toolInstances as $instance) {
            /** @var Tool $instance */
            $arr = $instance;
            $name = $arr['name'] ?? null;
            if (is_string($name) && $name !== '') {
                $toolsByName[$name] = $instance;
            }
        }

        // Ask the selected model for a response
        $httpResponse = Config::getModel($agent->model)->getResponse(
            instructions: $agent->instructions,
            input: $input,
            modelSettings: $agent->modelSettings,
            tools: $toolInstances,
        );

        // Wrap in RunResult
        $result = new RunResult($agent, $httpResponse);

        // Orchestrate tool execution here (not inside RunResult)
        $executed = self::executeFunctionToolCalls($agent, $result, $toolsByName);

        if (!empty($executed)) {
            $result->setExecutedToolResults($executed);
        }

        return $result;
    }

    /**
     * Execute all function tool calls returned by the model and return normalized results.
     * This mirrors the idea of Python's execute_function_tool_calls but in a simplified form
     * without guardrails or hooks.
     *
     * @param Agent $agent
     * @param RunResult $result
     * @param array<string, Tool> $toolsByName
     * @return array<int, array{call: ResponseFunctionToolCall, result: mixed}>
     */
    public static function executeFunctionToolCalls(Agent $agent, RunResult $result, array $toolsByName): array
    {
        $executed = [];
        foreach ($result->functionToolCalls() as $call) {
            $tool = $toolsByName[$call->name ?? ''] ?? null;
            if (!$tool instanceof Tool) {
                $executed[] = [
                    'call' => $call,
                    'result' => null,
                ];
                continue;
            }

            $args = self::decodeArguments($call);
            $schemaClass = $tool->schema();
            $schema = self::hydrateSchema($schemaClass, $args);
            $toolResult = $tool->handle($schema);

            $executed[] = [
                'call' => $call,
                'result' => $toolResult,
            ];
        }

        return $executed;
    }

    /**
     * Decode tool call arguments to array.
     *
     * @return array<string,mixed>
     */
    protected static function decodeArguments(ResponseFunctionToolCall $call): array
    {
        $args = $call->arguments;
        if (is_array($args)) {
            return $args;
        }
        if (is_string($args)) {
            $decoded = json_decode($args, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * Hydrate a simple DTO schema with given args (public properties only).
     *
     * @template TSchema of object
     * @param class-string<TSchema> $schemaClass
     * @param array<string,mixed> $args
     * @return TSchema
     */
    protected static function hydrateSchema(string $schemaClass, array $args): object
    {
        $schema = new $schemaClass();
        foreach ($args as $key => $value) {
            if (is_string($key) && property_exists($schema, $key)) {
                $schema->{$key} = $value;
            }
        }
        return $schema;
    }
}
