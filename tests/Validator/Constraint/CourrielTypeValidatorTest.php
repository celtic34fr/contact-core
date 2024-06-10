<?php

namespace Celtic34fr\ContactCore\Tests\Validator\Constraint;


use Celtic34fr\ContactCore\Validator\Constraint\CourrielType;
use Celtic34fr\ContactCore\Validator\Constraint\CourrielTypeValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CourrielTypeValidatorTest extends TestCase
{
    private $validator;
    private $context;

    protected function setUp(): void
    {
        $this->validator = new CourrielTypeValidator();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($this->context);
    }

    public function testValidateWithInvalidType()
    {
        $constraint = new CourrielType();
        $value = 123;

        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate($value, $constraint);
    }

    public function testValidateWithValidCourriel()
    {
        $constraint = new CourrielType();
        $value = 'test@test.com';

        $this->validator->validate($value, $constraint);

        // No exception should be thrown
        $this->addToAssertionCount(1);
    }

    public function testValidateWithInvalidCourriel()
    {
        $constraint = new CourrielType();
        $value = 'invalidemail';

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($builder);

        $builder->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', $value)
            ->willReturn($builder);

        $builder->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($value, $constraint);
    }
}