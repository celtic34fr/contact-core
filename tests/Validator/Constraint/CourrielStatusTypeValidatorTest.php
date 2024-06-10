<?php

namespace Celtic34fr\ContactCore\Tests\Validator\Constraint;

use Celtic34fr\ContactCore\Validator\Constraint\CourrielStatusType;
use Celtic34fr\ContactCore\Validator\Constraint\CourrielStatusTypeValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

class CourrielStatusTypeValidatorTest extends TestCase
{
    public function testValidation()
    {
        $validator = new CourrielStatusTypeValidator();
        $constraint = new CourrielStatusType();

        // Create a mock for the ExecutionContext
        $mockContext = $this->createMock(ExecutionContextInterface::class);
        /*$mockContext = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->getMock(); */
        $mockViolationBuilder = $this->getMockBuilder(ConstraintViolationBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set up the expectations for the Context mock
        $mockContext->method('buildViolation')->willReturn($mockViolationBuilder);
        $mockViolationBuilder->expects($this->once())->method('setParameter')->willReturnSelf();
        $mockViolationBuilder->expects($this->once())->method('addViolation');

        // Set the mocked context to the validator
        $validator->initialize($mockContext);

        // Invalid value
        $validator->validate("invalidStatus", $constraint);

        // Assert that a violation was created
        // You may add more specific assertions on the violation message or parameters
        // depending on your actual implementation
    }
}