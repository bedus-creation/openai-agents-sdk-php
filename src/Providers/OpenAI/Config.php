<?php

namespace JoBins\Agents\Providers\OpenAI;

use Illuminate\Http\Client\Factory as HttpFactory;
use JoBins\Agents\Models\Model;
use JoBins\Agents\Models\OpenAIResponsesModel;

class Config
{
    protected static string|null $apiKey = null;

    protected static string $baseUrl = 'https://api.openai.com';

    protected static bool $useResponseByDefault = true;

    protected static string $defaultModel = "gpt-5.1";

    /**
     * Shared HTTP factory for the OpenAI client, useful for tests to inject fakes.
     */
    protected static ?HttpFactory $http = null;

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

    /**
     * Inject a shared HTTP client factory (for testing/mocking).
     */
    public static function setHttp(?HttpFactory $http): void
    {
        self::$http = $http;
    }

    /**
     * Get the shared HTTP client factory if set.
     */
    public static function getHttp(): ?HttpFactory
    {
        return self::$http;
    }
}
