<?php

namespace SsnTestKit\Tests;

use stdClass;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestFailure;
use SsnTestKit\ElementHasAttribute;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\ExpectationFailedException;

class ElementHasAttributeTest extends TestCase
{
    protected function makeElement($name, $attributes = [])
    {
        $document = new DOMDocument('1.0', 'UTF-8');

        $element = $document->createElement($name);

        foreach ($attributes as $key => $value) {
            $element->setAttribute($key, $value);
        }

        return $element;
    }

    /** @test */
    public function it_covers_the_phpunit_basics()
    {
        $constraint = new ElementHasAttribute('class');

        $this->assertEquals('element has attribute', $constraint->toString());
        $this->assertCount(1, $constraint);

        // @todo Is it necessary to do some more thorough testing with LogicalNot?
        $this->assertThat($this->makeElement('p', ['class' => 'apples']), $constraint);
        $this->assertThat($this->makeElement('p'), new LogicalNot($constraint));

        $constraint = new ElementHasAttribute('class', 'apples');

        $this->assertThat($this->makeElement('p', ['class' => 'apples']), $constraint);
        $this->assertThat($this->makeElement('p'), new LogicalNot($constraint));
    }

    /** @test */
    public function it_handles_non_dom_element_values_gracefully()
    {
        $constraint = new ElementHasAttribute('class');

        $this->assertFalse($constraint->evaluate(null, '', true));
        $this->assertFalse($constraint->evaluate(true, '', true));
        $this->assertFalse($constraint->evaluate(15, '', true));
        $this->assertFalse($constraint->evaluate('string value', '', true));
        $this->assertFalse($constraint->evaluate([], '', true));
        $this->assertFalse($constraint->evaluate(new stdClass, '', true));

        try {
            $constraint->evaluate('string value');
        } catch (ExpectationFailedException $e) {
            $this->assertEquals(
                <<<EOF
Failed asserting that value is a DOMElement instance.

EOF
                ,
                TestFailure::exceptionToString($e)
            );

            return;
        }

        $this->fail();
    }

    /** @test */
    public function it_can_verify_that_an_attribute_is_present()
    {
        $constraint = new ElementHasAttribute('class');

        $this->assertFalse($constraint->evaluate($this->makeElement('p'), '', true));
        $this->assertTrue($constraint->evaluate($this->makeElement('p', [
            'class' => 'apples',
        ]), '', true));

        try {
            $constraint->evaluate($this->makeElement('p'));
        } catch (ExpectationFailedException $e) {
            $this->assertEquals(
                <<<EOF
Failed asserting that value has attribute "class".

EOF
                ,
                TestFailure::exceptionToString($e)
            );

            return;
        }

        $this->fail();
    }

    /** @test */
    public function it_can_verify_that_an_attribute_is_present_and_set_to_a_specific_value()
    {
        $constraint = new ElementHasAttribute('class', 'bananas');

        $this->assertFalse($constraint->evaluate($this->makeElement('p'), '', true));
        $this->assertFalse($constraint->evaluate($this->makeElement('p', [
            'class' => 'apples',
        ]), '', true));
        $this->assertTrue($constraint->evaluate($this->makeElement('p', [
            'class' => 'bananas',
        ]), '', true));

        try {
            $constraint->evaluate($this->makeElement('p'));
        } catch (ExpectationFailedException $e) {
            $this->assertEquals(
                <<<EOF
Failed asserting that value has attribute "class" set to "bananas".

EOF
                ,
                TestFailure::exceptionToString($e)
            );

            return;
        }

        $this->fail();
    }
}
