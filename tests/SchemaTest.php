<?php

namespace JoBins\Agents\Test;

use Illuminate\JsonSchema\Serializer;
use JoBins\Agents\Test\Fixtures\AgeSchema;
use JoBins\Agents\Test\Fixtures\ArraySchema;
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

        $this->assertEquals(['address', 'users'], $schema['required']);
        $this->assertSame('object', $schema['properties']['address']['type']);
        $this->assertSame(['street', 'city', 'country', 'color'], $schema['properties']['address']['required']);
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

    #[Test]
    public function test_array_fields_set_item_schema()
    {
        $schema = Serializer::serialize(ArraySchema::getProperties());

        $this->assertSame(['quantities', 'colors', 'addresses'], $schema['required']);

        $this->assertSame('array', $schema['properties']['quantities']['type']);
        $this->assertSame('integer', $schema['properties']['quantities']['items']['type']);
        $this->assertSame('List of quantities', $schema['properties']['quantities']['description']);

        $this->assertSame(['red', 'green', 'blue'], $schema['properties']['colors']['items']['enum']);
        $this->assertSame('array', $schema['properties']['colors']['type']);

        $this->assertSame('array', $schema['properties']['addresses']['type']);
        $this->assertSame('object', $schema['properties']['addresses']['items']['type']);
        $this->assertSame('Street address', $schema['properties']['addresses']['items']['properties']['street']['description']);
    }
}
