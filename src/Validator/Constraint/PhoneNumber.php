<?php

namespace Celtic34fr\ContactCore\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PhoneNumber extends Constraint
{
    public $badFormat = "Le numéro de téléphone n'est pas au bon format, courrifez";
    public $not10Number = "Le numéro de téléphone doit avoir 10 chiffres séparé ou non par des esâces ou des tirets";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
