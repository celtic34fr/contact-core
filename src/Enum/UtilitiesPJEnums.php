<?php

namespace Celtic34fr\ContactCore\Enum;

use Celtic34fr\ContactCore\Traits\EnumToArray;

enum UtilitiesPJEnums: string
{
    use EnumToArray;

    case Logo = 'LG';       // logo d'entreprise
    case Courriel = 'CR';   // pour un courriel
    case Network = 'LN';    // logo de réseau social


    public function _toString(): string
    {
        return (string) $this->value;
    }

    public static function isValid(string $value): bool
    {
        $utilitiesValuesTab = array_column(self::cases(), 'value');
        return in_array($value, $utilitiesValuesTab);
    }
}
