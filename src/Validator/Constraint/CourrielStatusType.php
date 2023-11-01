<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CourrielStatusType extends Constraint
{
    public $message = 'La valeur "{{ string }}" n\'est pas une valeur valide de statut d\'envoir de courriel.';
    public $mode = 'strict';
}
