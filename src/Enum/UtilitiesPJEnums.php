<?php

namespace Celtic34fr\ContactCore\Enum;

enum UtilitiesPJEnums: string
{
    case Logo = 'LG';       // logo d'entreprise
    case Courriel = 'CR';   // pour un courriel


    public function _toString(): string
    {
        return (string) $this->value;
    }

    public static function isValid(string $value): bool
    {
        $utilitiesValuesTab = array_column(self::cases(), 'value');
        return in_array($value, $utilitiesValuesTab);
    }

    public static function getCases(): array
    {
        $cases = self::cases();
        return array_map(static fn (\UnitEnum $case) => $case->name, $cases);
    }

    public static function getValues(): array
    {
        $cases = self::cases();
        return array_map(static fn (\UnitEnum $case) => $case->value, $cases);
    }
}
