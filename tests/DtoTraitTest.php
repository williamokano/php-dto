<?php

namespace Katapoka\Katapoka\Tests;

use InvalidArgumentException;
use Katapoka\Katapoka\Dto;
use PHPUnit_Framework_TestCase;

class DtoTraitTest extends PHPUnit_Framework_TestCase
{
    /** @var Dto */
    private $traitObject;

    public function setUp()
    {
        $this->traitObject = $this->createObjectForTrait();
    }

    public function testFill()
    {
        $this->assertObjectHasAttribute('dtoProperties', $this->traitObject, 'dtoProperties not found');
        $this->assertAttributeEquals([], 'dtoProperties', $this->traitObject, 'dtoProperties is not an array');

        $this->traitObject->fill(['a' => 1, 'b' => 2]);
        $this->assertObjectHasAttribute('dtoProperties', $this->traitObject, 'dtoProperties not found');
        $this->assertAttributeEquals(['a' => 1, 'b' => 2], 'dtoProperties', $this->traitObject, 'Properties not set to the dtoProperties array');

        $this->assertObjectHasAttribute('dtoChangedProperties', $this->traitObject, 'dtoChangedProperties not found');
        $this->assertAttributeEquals(['a' => true, 'b' => true], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties is not an array');

        $this->traitObject->fill(['c' => 3, 'b' => 5]);
        $this->assertObjectHasAttribute('dtoProperties', $this->traitObject, 'dtoProperties not found');
        $this->assertAttributeEquals(['a' => 1, 'b' => 5, 'c' => 3], 'dtoProperties', $this->traitObject, 'dtoProperties incorrect set');
        $this->assertObjectHasAttribute('dtoChangedProperties', $this->traitObject, 'dtoChangedProperties not found');
        $this->assertAttributeEquals(['a' => 1, 'b' => 5, 'c' => 3], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties did not flagged "b" as changed');

        $this->traitObject->fill(['d' => 4], true);
        $this->assertObjectHasAttribute('dtoProperties', $this->traitObject, 'dtoProperties not found');
        $this->assertAttributeEquals(['d' => 4], 'dtoProperties', $this->traitObject, 'dtoProperties did not replace the elements for the "d" property only');
        $this->assertObjectHasAttribute('dtoChangedProperties', $this->traitObject, 'dtoChangedProperties not found');
        $this->assertAttributeEquals(['d' => true], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties did not reseted as exptected');
    }

    public function testGet()
    {
        $this->traitObject->fill(['d' => 4], true);
        $this->assertObjectHasAttribute('dtoProperties', $this->traitObject, 'dtoProperties not found');
        $this->assertAttributeEquals(['d' => 4], 'dtoProperties', $this->traitObject, 'dtoProperties did not replace the elements for the "d" property only');
        $this->assertObjectHasAttribute('dtoChangedProperties', $this->traitObject, 'dtoChangedProperties not found');
        $this->assertAttributeEquals(['d' => 4], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties did not reseted as exptected');
        $this->assertEquals(4, $this->traitObject->d, 'Value is not 4');
        $this->assertEquals(4, $this->traitObject->get('d', null), 'Value is not 4');
        $this->assertNull($this->traitObject->get('okano'), 'The returned value is not null');
        $this->assertEquals(666, $this->traitObject->get('okano', 666), 'The returned value is not 666');
    }

    public function testSet()
    {
        $this->traitObject->lastName = 'OKANO';
        $this->assertEquals('OKANO', $this->traitObject->lastName, 'The last name is not OKANO');
        $this->assertEquals('OKANO', $this->traitObject->get('lastName'), 'The last name is not OKANO');

        $this->traitObject->set('string', []);
        $this->assertEquals([], $this->traitObject->string);

        try {
            $this->traitObject->set(new \stdClass(), []);
            $this->fail('Should have raised an invalid argument exception');
        } catch (InvalidArgumentException $e) {
            $this->assertStringStartsWith('The property is an invalid name for properties', $e->getMessage(), 'The exception message does not match');
        }
    }

    public function testGetAllProperties()
    {
        $this->traitObject->a = 'William';
        $this->traitObject->b = 'Okano';
    }

    public function testChanged()
    {
        $this->traitObject->fill(['d' => 4], true);
        $this->assertObjectHasAttribute('dtoProperties', $this->traitObject, 'dtoProperties not found');
        $this->assertAttributeEquals(['d' => 4], 'dtoProperties', $this->traitObject, 'dtoProperties did not replace the elements for the "d" property only');
        $this->assertObjectHasAttribute('dtoChangedProperties', $this->traitObject, 'dtoChangedProperties not found');
        $this->assertAttributeEquals(['d' => 4], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties did not reseted as exptected');

        // Change with set
        $this->traitObject->set('d', 5);
        $this->assertAttributeEquals(['d' => 5], 'dtoProperties', $this->traitObject, 'dtoProperties did not changed the value for 5');
        $this->assertTrue($this->traitObject->changed('d'), 'The property "d" was not flagged as changed');

        // Change with fill without replace
        $this->traitObject->fill(['d' => 'okano']);
        $this->assertAttributeEquals(['d' => 'okano'], 'dtoProperties', $this->traitObject, 'dtoProperties did not changed the value for 5');
        $this->assertTrue($this->traitObject->changed('d'), 'The property "d" was not flagged as changed');
        $this->assertEquals('okano', $this->traitObject->get('d'));

        // Set as clean
        $this->traitObject->cleanProperty('d');
        $this->assertObjectHasAttribute('dtoChangedProperties', $this->traitObject, 'dtoChangedProperties not found');
        $this->assertAttributeEquals(['d' => false], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties did not reseted as exptected');
        $this->assertFalse($this->traitObject->changed('d'), 'Property "d" flagged as changed yet');

        // Change with fill with replace, should reset changed
        $this->traitObject->fill(['d' => 'okano'], true);
        $this->assertAttributeEquals(['d' => 'okano'], 'dtoProperties', $this->traitObject, 'dtoProperties did not changed the value for 5');
        $this->assertTrue($this->traitObject->changed('d'), 'The property "d" was not flagged as changed');
        $this->assertEquals('okano', $this->traitObject->get('d'));
    }

    private function createObjectForTrait()
    {
        $traitName = Dto::class;

        return $this->getObjectForTrait($traitName);
    }
}
