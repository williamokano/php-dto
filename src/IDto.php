<?php

namespace Katapoka\Katapoka;

interface IDto
{
    /**
     * Given an data array with Key/Value pair, set the property as the key with the given value.
     *
     * @param array $data Key/Value
     * @param bool $replace Pass true if you want to replace all the properties by the new $data array
     *
     * @return $this
     */
    public function fill(array $data = [], $replace = false);

    /**
     * Given a property, which should be null or an scalar value.
     *
     * @param int|float|null|string|bool $property A scalar value or null
     * @param mixed $value Can be anything
     *
     * @return $this
     */
    public function set($property, $value);

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
    public function get($property, $default = null);

    /**
     * Get all properties of the DTO object.
     *
     * @return array
     */
    public function getProperties();

    /**
     * Check if the given property exists.
     *
     * @param int|float|null|string|bool $property
     *
     * @return bool
     */
    public function hasProperty($property);

    /**
     * Check if the given property has changed.
     *
     * @param int|float|null|string|bool $property
     *
     * @return mixed
     */
    public function changed($property);

    /**
     * Resets the DTO cleaning all properties set and settings all the properties as non-changed.
     *
     * @return $this
     */
    public function reset();

    /**
     * Set a specific property as non-changed. Best used when using along with active record and the values are all saved.
     *
     * @param int|float|null|string|bool $property
     *
     * @return $this
     */
    public function cleanProperty($property);

    /**
     * Set all properties as non-changed. Best used when using along with active record and the values are all saved.
     *
     * @return $this
     */
    public function cleanAll();
}