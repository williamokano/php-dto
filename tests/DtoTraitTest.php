<?php
namespace Katapoka\Katapoka\Tests;

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
        $this->assertAttributeEquals([], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties is not an array');

        $this->traitObject->fill(['c' => 3, 'b' => 5]);
        $this->assertObjectHasAttribute('dtoProperties', $this->traitObject, 'dtoProperties not found');
        $this->assertAttributeEquals(['a' => 1, 'b' => 5, 'c' => 3], 'dtoProperties', $this->traitObject, 'dtoProperties incorrect set');
        $this->assertObjectHasAttribute('dtoChangedProperties', $this->traitObject, 'dtoChangedProperties not found');
        $this->assertAttributeEquals(['b' => 1], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties did not flagged "b" as changed');

        $this->traitObject->fill(['d' => 4], true);
        $this->assertObjectHasAttribute('dtoProperties', $this->traitObject, 'dtoProperties not found');
        $this->assertAttributeEquals(['d' => 4], 'dtoProperties', $this->traitObject, 'dtoProperties did not replace the elements for the "d" property only');
        $this->assertObjectHasAttribute('dtoChangedProperties', $this->traitObject, 'dtoChangedProperties not found');
        $this->assertAttributeEquals([], 'dtoChangedProperties', $this->traitObject, 'dtoChangedProperties did not reseted as exptected');
    }

    private function createObjectForTrait()
    {
        $traitName = Dto::class;

        return $this->getObjectForTrait($traitName);
    }
}