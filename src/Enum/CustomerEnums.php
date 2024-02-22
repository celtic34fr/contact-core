<?php

namespace Celtic34fr\ContactCore\Enum;

use Celtic34fr\ContactCore\Traits\EnumToArray;

enum CustomerEnums: string
{
    use EnumToArray;

    case Client         = 'CL';
    case Prospect       = 'PP';
    case Fournisseur    = 'FR';
    case Partner        = 'PR';

    public function _toString(): string
    {
        return (string) $this->value;
    }

    public static function isValid(string $value): bool
    {
        $courrielValuesTab = array_column(self::cases(), 'value');
        return in_array($value, $courrielValuesTab);
    }
}
