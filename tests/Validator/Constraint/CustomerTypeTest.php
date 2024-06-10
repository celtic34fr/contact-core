<?php

namespace Celtic34fr\ContactCore\Tests\Validator\Constraint;

use Celtic34fr\ContactCore\Validator\Constraint\CustomerType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CustomerTypeTest extends TestCase
{
    public function testMessage()
    {
        $constraint = new CustomerType();
        $this->assertEquals('La valeur "{{ string }}" n\'est pas une valeur valide de type de clientèle.', $constraint->message);
    }

    public function testMode()
    {
        $constraint = new CustomerType();
        $this->assertEquals('strict', $constraint->mode);
    }
}
