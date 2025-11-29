> ⚠️ This package is in an early development phase. Feedback and contributions are welcome.

# OpenAI Agents PHP
OpenAI Agents PHP is a lightweight SDK for building AI agents and tool-driven workflows in PHP.
It provides a simple architecture for defining agents, attaching custom tools, validating structured inputs with schemas, and executing conversations powered by OpenAI models.

### Features
- [x] Agent
- [x] Runner
- [ ] Tools: WIP
- [ ] Structure Response: WIP
- [x] Json Schema
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

### Basic Usage

```php
use JoBins\Agents\Agents\Agent;

class JoBinsAgent extends Agent
{
    public string $name = "JoBins Agent";

    public ?string $instructions = <<<'TEXT'
                       Handle all direct user communication. 
                       Call the relevant tools when specialized expertise is needed.
                       TEXT;

    public array $tools = [
        PasswordUpdateTool::class,
        UserCreateTool::class
    ];
}
```

Define the Schema

```php
class UserCreateSchema extends Schema
{
    #[Field(description: "Fullname of the user")]
    #[Size(min: 1, max: 50)]
    public string $name;

    #[Field(description: "Email of the person")]
    #[Format('email')]
    public ?int $email;

    #[Field(description: "Password")]
    #[Size(min: 1, max: 50)]
    public string $password;
}
```

Attach the schema in the tool

```php
class UserCreateTool extends Tool
{
    function schema(): string
    {
        return UserCreateSchema::class;
    }

    /**
     * @param UserCreateSchema $schema
     */
    function handle($schema): array|Response {}
}
```
