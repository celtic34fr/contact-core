<?php

namespace Celtic34fr\ContactCore\Tests\Validator\Constraint;

use Celtic34fr\ContactCore\Validator\Constraint\CourrielStatusType;
use PHPUnit\Framework\TestCase;

class CourrielStatusTypeTest extends TestCase
{
    public function testMessageIsSet()
    {
        $constraint = new CourrielStatusType();
        $this->assertEquals('La valeur "{{ string }}" n\'est pas une valeur valide de statut d\'envoi de courriel.', $constraint->message);
    }

    public function testModeIsSet()
    {
        $constraint = new CourrielStatusType();
        $this->assertEquals('strict', $constraint->mode);
    }
}
