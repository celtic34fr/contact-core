<?php

namespace Celtic34fr\ContactCore\Tests\Validator\Constraint;

use Celtic34fr\ContactCore\Validator\Constraint\PhoneNumberValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

class PhoneNumberValidatorTest extends TestCase
{
    public function testValidateWithInvalidPhoneNumberLength(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $constraint = new class extends Constraint {
            public $country = 'FR';
            public $badFormat = 'Invalid phone number format';
            public $not10Number = 'Phone number must be 10 digits';
        };
        $validator = new PhoneNumberValidator();
        $validator->initialize($context);
        
        $context->method('buildViolation')
            ->willReturn(new ConstraintViolationBuilder(new ConstraintViolationList(), $constraint));

        $validator->validate('1234567', $constraint);

        // Assert that a violation is added for invalid phone number length
        $this->assertCount(1, $context->getViolations());
        $this->assertEquals('Invalid phone number format', $context->getViolations()[0]->getMessage());
    }

    public function testValidateWithValidPhoneNumber(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $constraint = new class extends Constraint {
            public $country = 'FR';
            public $badFormat = 'Invalid phone number format';
            public $not10Number = 'Phone number must be 10 digits';
        };
        $validator = new PhoneNumberValidator();
        $validator->initialize($context);
        
        $context->method('buildViolation')
            ->willReturn(new ConstraintViolationBuilder(new ConstraintViolationList(), $constraint));

        $validator->validate('0123456789', $constraint);

        // Assert that no violations are added for a valid phone number
        $this->assertCount(0, $context->getViolations());
    }
}
