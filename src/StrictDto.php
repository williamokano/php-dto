<?php
namespace Katapoka\Katapoka;

use InvalidArgumentException;

trait StrictDto
{
    use Dto {
        set as private dtoSet;
    }

    /**
     * Given a property, which should be null or an scalar value.
     *
     * @param int|float|null|string|bool $property A scalar value or null
     * @param mixed $value Can be anything
     *
     * @return $this
     */
    public function set($property, $value)
    {
        if ($this->checkPropertyConstraint($property, $value)) {
            return $this->dtoSet($property, $value);
        }

        if ($this->shouldThrowException()) {
            $constraint = $this->getPropertyConstraint($property);
            if (is_callable($constraint)) {
                throw new InvalidArgumentException(sprintf('The property "%s" failed on the callback constraint check', $property));
            } else {
                throw new InvalidArgumentException(sprintf('The property "%s" failed on the constraint check. Must be a type of "%s", given type "%s"', $property, $constraint, gettype($value)));
            }
        }

        return $this;
    }

    /**
     * Check if there is any constraint set to the given property.
     *
     * @param int|float|null|string|bool $property A scalar value or null
     *
     * @return bool
     */
    public function hasConstraint($property)
    {
        return array_key_exists($property, $this->getConstraints());
    }

    /**
     * Return the property constraint.
     *
     * @param int|float|null|string|bool $property A scalar value or null
     *
     * @return null|string|callable
     */
    public function getPropertyConstraint($property)
    {
        if ($this->hasConstraint($property)) {
            return $this->getConstraints()[$property];
        }

        return null;
    }

    /**
     * Method where you should override in order to set the DTO constraints.
     *
     * @return array
     */
    abstract public function getConstraints();

    /**
     * Check if when the constraint fails if it should raise an exception or not.
     *
     * @return bool
     */
    private function shouldThrowException()
    {
        return property_exists($this, 'shouldThrowException') && $this->shouldThrowException;
    }

    /**
     * Add constraints to enforce the type of the value.
     *
     * @param int|float|null|string|bool $property A scalar value or null
     * @param mixed $value
     *
     * @return bool
     */
    private function checkPropertyConstraint($property, $value)
    {
        if ($this->hasConstraint($property)) {
            $constraint = $this->getPropertyConstraint($property);

            if (is_callable($constraint)) {
                $response = $constraint($value);
                if (!is_bool($response)) {
                    throw new InvalidArgumentException('The callback must return a boolean');
                }

                return $response;
            }

            return gettype($value) === $constraint;
        }

        return true;
    }
}
