# OpenAI Agents PHP

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
<?php

class ExampleSchema {
    public function __construct(
        public string $name
    ){}
}

class ExampleTool extends \JoBins\Agents\Tools\Tool{
    public function schema(): {
        return ExampleSchema::class;
    }
}
```
