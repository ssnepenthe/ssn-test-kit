<?php

namespace SsnTestKit;

use DOMElement;
use PHPUnit\Framework\Constraint\Constraint;

class ElementHasAttribute extends Constraint
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string|null
     */
    protected $value;

    public function __construct(string $attribute, string $value = null)
    {
        $this->attribute = $attribute;
        $this->value = $value;
    }

    public function toString() : string
    {
        return 'element has attribute';
    }

    /**
     * @param mixed $other
     */
    protected function failureDescription($other) : string
    {
        if (! $other instanceof DOMElement) {
            return 'value is a DOMElement instance';
        }

        if (null === $this->value) {
            return "value has attribute \"{$this->attribute}\"";
        }

        return "value has attribute \"{$this->attribute}\" set to \"{$this->value}\"";
    }

    /**
     * @param mixed $other
     */
    protected function matches($other) : bool
    {
        if (! $other instanceof DOMElement) {
            return false;
        }

        return $other->hasAttribute($this->attribute) && (
            null === $this->value || $other->getAttribute($this->attribute) === $this->value
        );
    }
}
