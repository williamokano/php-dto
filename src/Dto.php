<?php

namespace Katapoka\Katapoka;

use InvalidArgumentException;

/**
 * Trait that adds support to auto generated properties
 */
trait Dto
{
    /**
     * List that holds all the DTO auto properties.
     *
     * @var array
     */
    private $dtoProperties = [];

    /**
     * List containing all the dirty properties. To be considered as dirty, the property should have change after
     * the first set.
     *
     * @var array
     */
    private $dtoChangedProperties = [];

    /**
     * Given an data array with Key/Value pair, set the property as the key with the given value.
     *
     * @param array $data Key/Value
     * @param bool $replace Pass true if you want to replace all the properties by the new $data array
     *
     * @return $this
     */
    public function fill(array $data = [], $replace = false)
    {
        if ($replace) {
            $this->reset();
        }

        foreach ($data as $property => $value) {
            $this->set($property, $value);
        }

        return $this;
    }

    /**
     * Given some property, which should be null or an scalar value.
     *
     * @param int|float|null|string|bool $property A scalar value or null
     * @param mixed $value Can be anything
     *
     * @return $this
     */
    public function set($property, $value)
    {
        $this->validateProperty($property);
        if (!$this->hasProperty($property) || ($this->hasProperty($property) && $this->get($property) !== $value)) {
            $this->flagAsDirty($property);
        }

        $this->dtoProperties[$property] = $value;

        return $this;
    }

    /**
     * Given a property, get his value, if exists, or return the $default value.
     *
     * If the given property doesn't have a legal name, a expcetion will be throwed
     *
     * @param int|float|null|string|bool $property
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($property, $default = null)
    {
        $this->validateProperty($property);
        if ($this->hasProperty($property)) {
            return $this->dtoProperties[$property];
        }

        return $default;
    }

    /**
     * Get all properties of the DTO object.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->dtoProperties;
    }

    /**
     * Checks if the given property exists.
     *
     * @param int|float|null|string|bool $property
     *
     * @return bool
     */
    public function hasProperty($property)
    {
        $this->validateProperty($property);

        return array_key_exists($property, $this->dtoProperties);
    }

    /**
     * Check if the given property has changed.
     *
     * @param int|float|null|string|bool $property
     *
     * @return mixed
     */
    public function changed($property)
    {
        $this->validateProperty($property);
        if (!$this->hasProperty($property)) {
            throw new InvalidArgumentException(sprintf('Property %s doesn\'t exist on this object', $property));
        }

        return array_key_exists($property, $this->dtoChangedProperties) && $this->dtoChangedProperties[$property];
    }

    /**
     * Returns an array containing only the properties marked as changed.
     *
     * @return array
     */
    public function getChangedProperties()
    {
        $changedProperties = array_keys(array_filter($this->dtoChangedProperties, function ($changed) {
            return !!$changed;
        }));

        return array_reduce($changedProperties, function ($acc, $property) {
            $acc[$property] = $this->dtoProperties[$property];

            return $acc;
        }, []);
    }

    /**
     * Resets the DTO cleaning all properties set and settings all the properties as non-changed.
     *
     * @return $this
     */
    public function reset()
    {
        $this->dtoProperties = [];
        $this->dtoChangedProperties = [];

        return $this;
    }

    /**
     * Set a specific property as non-changed. Best used when using along with active record and the values are all saved.
     *
     * @param int|float|null|string|bool $property
     *
     * @return $this
     */
    public function cleanProperty($property)
    {
        $this->validateProperty($property);
        if (array_key_exists($property, $this->dtoChangedProperties)) {
            $this->dtoChangedProperties[$property] = false;
        }

        return $this;
    }

    /**
     * Set all properties as non-changed. Best used when using along with active record and the values are all saved.
     *
     * @return $this
     */
    public function cleanAll()
    {
        foreach (array_keys($this->dtoChangedProperties) as $property) {
            $this->cleanProperty($property);
        }

        return $this;
    }

    /**
     * Magic method to get value from the object.
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * Magic method do set value to the object property.
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->set($property, $value);
    }

    /**
     * Check if the property has valid name.
     *
     * @param mixed $property
     *
     * @throws InvalidArgumentException
     */
    private function validateProperty($property)
    {
        if (!$this->isKeyValid($property)) {
            throw new InvalidArgumentException(sprintf('The property is an invalid name for properties'));
        }
    }

    /**
     * Check if the given key is valid for property.
     *
     * @param mixed $key Anything
     *
     * @return bool
     */
    private function isKeyValid($key)
    {
        return is_null($key) || is_scalar($key);
    }

    /**
     * Flag a property as changed.
     *
     * @param int|float|null|string|bool $property
     */
    private function flagAsDirty($property)
    {
        $this->dtoChangedProperties[$property] = true;
    }
}
