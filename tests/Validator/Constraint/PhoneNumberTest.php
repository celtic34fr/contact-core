<?php

namespace Celtic34fr\ContactCore\Tests\Validator\Constraint;

use Celtic34fr\ContactCore\Validator\Constraint\PhoneNumber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

class PhoneNumberTest extends TestCase
{
    public function testPhoneNumberHasBadFormat()
    {
        $constraint = new PhoneNumber();
        $this->assertEquals("Le numéro de téléphone n'est pas au bon format, corrigez-le", $constraint->badFormat);
    }

    public function testPhoneNumberHasNot10Number()
    {
        $constraint = new PhoneNumber();
        $this->assertEquals("Le numéro de téléphone doit avoir 10 chiffres séparé ou non par des espaces ou des tirets", $constraint->not10Number);
    }

    public function testGetTargetsReturnsClassConstraint()
    {
        $constraint = new PhoneNumber();
        $this->assertEquals(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
