<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\Enum;

use Bolt\Extension\Celtic34fr\ContactCore\Traits\EnumToArray;

enum CourrielEnums: string
{
    use EnumToArray;

    case Contact = 'QR';    // demande d'informations

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
