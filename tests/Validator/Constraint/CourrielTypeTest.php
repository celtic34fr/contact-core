<?php

namespace Celtic34fr\ContactCore\Tests\Validator\Constraint;

use Celtic34fr\ContactCore\Validator\Constraint\CourrielType;
use PHPUnit\Framework\TestCase;

class CourrielTypeTest extends TestCase {
    public function testMessageIsCorrect() {
        $constraint = new CourrielType();
        $this->assertEquals('La valeur "{{ string }}" n\'est pas une valeur valide de nature de courriel.', $constraint->message);
    }
    
    public function testModeIsCorrect() {
        $constraint = new CourrielType(); $this->assertEquals('strict', $constraint->mode);
    }
}