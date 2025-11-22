<?php

namespace JoBins\Agents\Providers\OpenAI;

use JoBins\Agents\Models\Model;
use JoBins\Agents\Models\OpenAIResponsesModel;

class Config
{
    protected static string|null $apiKey = null;

    protected static string $baseUrl = 'https://api.openai.com';

    protected static bool $useResponseByDefault = true;

    protected static string $defaultModel = "gpt-5.1";

    public static function getApiKey(): ?string
    {
        return self::$apiKey;
    }

    public static function useApiKey(string $key): void
    {
        self::$apiKey = $key;
    }

    public static function getClient(): Client
    {
        return new Client(url: self::$baseUrl, apiKey: self::$apiKey);
    }

    public static function getDefaultModel(): string
    {
        return self::$defaultModel;
    }

    public static function getModel(?string $model): Model
    {
        return new OpenAIResponsesModel(model: $model, client: self::getClient());
    }
}
