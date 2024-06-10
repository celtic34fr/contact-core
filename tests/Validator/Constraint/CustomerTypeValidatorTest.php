<?php

namespace Celtic34fr\ContactCore\Tests\Validator\Constraint;

use Celtic34fr\ContactCore\Validator\Constraint\CustomerType;
use Celtic34fr\ContactCore\Validator\Constraint\CustomerTypeValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Constraint;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CustomerTypeValidatorTest extends TestCase
{
    public function testValidateWithInvalidConstraint()
    {
        $validator = new CustomerTypeValidator();
        $constraint = new Constraint();
        $this->expectException(UnexpectedTypeException::class);
        
        $validator->validate('test', $constraint);
    }

    public function testValidateWithNullValue()
    {
        $validator = new CustomerTypeValidator();
        $value = null;
        $constraint = new CustomerType();

        $context = $this->createMock(ExecutionContext::class);
        $validator->initialize($context);

        $validator->validate($value, $constraint);
        
        // Assert no violations are added
        $this->assertEmpty($context->getViolations());
    }

    public function testValidateWithEmptyValue()
    {
        $validator = new CustomerTypeValidator();
        $value = '';
        $constraint = new CustomerType();

        $context = $this->createMock(ExecutionContext::class);
        $validator->initialize($context);

        $validator->validate($value, $constraint);
        
        // Assert no violations are added
        $this->assertEmpty($context->getViolations());
    }

    public function testValidateWithInvalidValue()
    {
        $validator = new CustomerTypeValidator();
        $value = 'invalid_value';
        $constraint = new CustomerType();

        $context = $this->createMock(ExecutionContext::class);
        $validator->initialize($context);

        $validator->validate($value, $constraint);

        // Assert a violation is added
        $violations = $context->getViolations();
        $this->assertCount(1, $violations);
        $this->assertEquals('Customer type is not valid', $violations[0]->getMessage());
    }

    // Add more test cases as needed...
}