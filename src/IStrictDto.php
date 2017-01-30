<?php

namespace Katapoka\Katapoka;

interface IStrictDto extends IDto
{
    /**
     * Check if there is any constraint set to the given property.
     *
     * @param int|float|null|string|bool $property A scalar value or null
     *
     * @return bool
     */
    public function hasConstraint($property);

    /**
     * Return the property constraint.
     *
     * @param int|float|null|string|bool $property A scalar value or null
     *
     * @return null|string|callable
     */
    public function getPropertyConstraint($property);

    /**
     * Method where you should override in order to set the DTO constraints.
     *
     * @return array
     */
    public function getConstraints();
}
