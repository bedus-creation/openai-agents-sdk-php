<?php

namespace JoBins\Agents\Test;

use JoBins\Agents\Test\Fixtures\UserCreateSchema;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    /**
     * @test
     */
    public function test_can_generate_json_schema_from_class()
    {
        $expected = [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'The name of the person to refund.',
                    'format' => 'email',
                    'minLength' => 1,
                    'maxLength' => 100,
                ],
                'count' => [
                    'type' => 'integer',
                    'description' => 'The number of items to refund.',
                ],
            ],
            'required' => ['name'],
        ];

        $this->assertEquals($expected, UserCreateSchema::toJsonSchema());
    }
}
