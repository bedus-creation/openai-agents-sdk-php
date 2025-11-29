<?php

namespace JoBins\Agents\Test;

use Illuminate\JsonSchema\Serializer;
use JoBins\Agents\Test\Fixtures\AgeSchema;
use JoBins\Agents\Test\Fixtures\PasswordUpdateSchema;
use JoBins\Agents\Test\Fixtures\StatusEnumSchema;
use JoBins\Agents\Test\Fixtures\UserCreateSchema;
use JoBins\Agents\Test\Fixtures\UserProfileSchema;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    #[Test]
    public function test_can_generate_json_schema_from_class()
    {
        $expected = [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'description' => 'Fullname of the user',
                    'minLength' => 1,
                    'maxLength' => 50,
                    'type' => 'string',
                ],
                'email' => [
                    'description' => 'Email of the person',
                    'format' => 'email',
                    'type' => 'string',
                ],
                'password' => [
                    'description' => 'Password',
                    'minLength' => 1,
                    'maxLength' => 50,
                    'type' => 'string',
                ],
            ],
            'required' => ['name','password'],
        ];

        $actual = Serializer::serialize(UserCreateSchema::getProperties());
        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function test_nullable_properties_are_not_marked_required()
    {
        $schema = Serializer::serialize(UserCreateSchema::getProperties());

        $this->assertArrayHasKey('required', $schema);
        $this->assertNotContains('email', $schema['required']);
        $this->assertSame('string', $schema['properties']['email']['type']);
        $this->assertSame('email', $schema['properties']['email']['format']);
    }

    #[Test]
    public function test_size_constraints_are_applied_to_string_fields()
    {
        $schema = Serializer::serialize(PasswordUpdateSchema::getProperties());

        $this->assertSame(8, $schema['properties']['password']['minLength']);
        $this->assertArrayNotHasKey('minLength', $schema['properties']['confirmPassword']);
        $this->assertEquals(['password', 'confirmPassword'], $schema['required']);
    }

    #[Test]
    public function test_integer_field_includes_field_metadata_and_is_required()
    {
        $schema = Serializer::serialize(AgeSchema::getProperties());

        $this->assertSame('integer', $schema['properties']['age']['type']);
        $this->assertSame('Age', $schema['properties']['age']['title']);
        $this->assertSame('Age of the account', $schema['properties']['age']['description']);
        $this->assertEquals(['age'], $schema['required']);
    }

    #[Test]
    public function test_can_embed_nested_schema_as_object_property()
    {
        $schema = Serializer::serialize(UserProfileSchema::getProperties());

        $this->assertEquals(['address'], $schema['required']);
        $this->assertSame('object', $schema['properties']['address']['type']);
        $this->assertSame(['street', 'city', 'country'], $schema['properties']['address']['required']);
        $this->assertSame('Street address', $schema['properties']['address']['properties']['street']['description']);
        $this->assertSame(['US', 'CA'], $schema['properties']['address']['properties']['country']['enum']);
        $this->assertNotContains('website', $schema['required']);
        $this->assertSame('string', $schema['properties']['website']['type']);
    }

    #[Test]
    public function test_enum_attribute_sets_allowed_values_for_string_and_integer()
    {
        $schema = Serializer::serialize(StatusEnumSchema::getProperties());

        $this->assertSame(['pending', 'active', 'disabled'], $schema['properties']['status']['enum']);
        $this->assertSame(['status', 'priority'], $schema['required']);
        $this->assertSame(['integer', [1, 2, 3]], [$schema['properties']['priority']['type'], $schema['properties']['priority']['enum']]);
    }
}
