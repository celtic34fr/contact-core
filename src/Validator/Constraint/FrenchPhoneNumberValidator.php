<?php

namespace Celtic34fr\ContactCore\Validator\Constraint;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FrenchPhoneNumberValidator extends ConstraintValidator
{
    public function validate($phoneNumber, Constraint $constraint): void
    {
        $country = $constraint->country;
        switch($country) {
            case 'FR':
                if (strlen($phoneNumber) < 10) {
                    $this->context
                        ->buildViolation($constraint->badFormat)
                        ->addViolation();
                }
                $phoneNumber = $this->getOnlyNumbers($phoneNumber);
                if (strlen($phoneNumber) < 10 || strlen($phoneNumber) > 10) {
                    $this->context
                        ->buildViolation($constraint->not10Number)
                        ->addViolation();
                }
                break;
            default:
                throw new Exception("$country code pays non traité");
                break;
        }
    }


    private function getOnlyNumbers(string $phoneNumber) :string
    {
        return filter_var($phoneNumber, FILTER_SANITIZE_NUMBER_INT);
    }
}
