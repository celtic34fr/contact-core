<?php

namespace Celtic34fr\ContactCore\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CustomerType extends Constraint
{
    public $message = 'La valeur "{{ string }}" n\'est pas une valeur valide de type de clientèle.';
    public $mode = 'strict';
}
