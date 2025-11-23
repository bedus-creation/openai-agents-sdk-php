> ⚠️ This package is in an early development phase. Feedback and contributions are welcome.

# OpenAI Agents PHP
OpenAI Agents PHP is a lightweight SDK for building AI agents and tool-driven workflows in PHP.
It provides a simple architecture for defining agents, attaching custom tools, validating structured inputs with schemas, and executing conversations powered by OpenAI models.

### Features
- [x] Agent
- [x] Runner
- [ ] Tools
- [ ] Structure Response
- [ ] Memory

### Getting Started
```php
<?php

use JoBins\Agents\Providers\OpenAI\Config;

Config::useApiKey('YOUR_API_TOKEN');

$agent = \JoBins\Agents\Agent(
    name: "Assistant", 
    instructions: "You are assistant"
);

\JoBins\Agents\Runner::run($agent, "Hello");
```

### Tools

```php
use JoBins\Agents\Agents\Agent;

class CustomerFacingAgent extends Agent
{
    public static function create(): CustomerFacingAgent
    {
        return new self(
            name: "Customer Facing Agent",
            instructions: <<<'TEXT'
                Handle all direct user communication. 
                Call the relevant tools when specialized expertise is needed.
                TEXT,
            tools: [
                BookingTool::class,
                RefundTool::class
            ]
        );
    }
}
```

Define the Schema

```php
class RefundSchema extends Schema
{
    #[Field(description: "The name of the person to refund.", format: 'email', minLength: 1, maxLength: 100)]
    public string $name;

    #[Field(description: "The number of items to refund.")]
    public ?int $count;
}
```

Attach the schema in the tool
```php

class RefundTool
{
    function schema(): string
    {
        return RefundSchema::class;
    }

    function handle(RefundSchema $schema): array|Response
    {

    }
}
```
