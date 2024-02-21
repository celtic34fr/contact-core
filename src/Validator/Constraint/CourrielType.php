<?php

namespace Celtic34fr\ContactCore\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CourrielType extends Constraint
{
    public $message = 'La valeur "{{ string }}" n\'est pas une valeur valide de nature de courriel.';
    public $mode = 'strict';
}
