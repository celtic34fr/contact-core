<?php

namespace Celtic34fr\ContactCore\Tests\FormEntity;

use Celtic34fr\ContactCore\FormEntity\ParameterFE;
use PHPUnit\Framework\TestCase;

class ParameterFETest extends TestCase
{
    private ParameterFE $parameterFE;

    protected function setUp(): void
    {
        $this->parameterFE = new ParameterFE();
    }

    public function testSetNameAndGetname(): void
    {
        $name = 'Parameter';
        $this->parameterFE->setName($name);
        $this->assertEquals($name, $this->parameterFE->getName());
    }

    public function testSetDescriptionAndGetDescription(): void
    {
        $description = 'This is a parameter';
        $this->parameterFE->setDescription($description);
        $this->assertEquals($description, $this->parameterFE->getDescription());
    }

    public function testSetValuesAndGetValues(): void
    {
        $values = ['value1', 'value2', 'value3'];
        $this->parameterFE->setValues($values);
        $this->assertEquals($values, $this->parameterFE->getValues());
    }

    public function testAddValue(): void
    {
        $value = 'new value';
        $this->parameterFE->addValue($value);
        $values = $this->parameterFE->getValues();
        $this->assertContains($value, $values);
    }

    public function testRemoveValue(): void
    {
        $values = ['value1', 'value2', 'value3'];
        $this->parameterFE->setValues($values);

        $valueToRemove = 'value2';
        $this->parameterFE->removeValue($valueToRemove);
        $valuesAfterRemoval = $this->parameterFE->getValues();

        $this->assertNotContains($valueToRemove, $valuesAfterRemoval);
        $this->assertCount(count($values) - 1, $valuesAfterRemoval);
    }
}
