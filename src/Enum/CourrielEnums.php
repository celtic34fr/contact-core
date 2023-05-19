<?php

namespace Celtic34fr\ContactCore\Enum;

enum CourrielEnums: string
{
    case Contact = 'QR';

    public function _toString(): string
    {
        return (string) $this->value;
    }

    public static function isValid(string $value): bool
    {
        $courrielValuesTab = array_column(self::cases(), 'value');
        return in_array($value, $courrielValuesTab);
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
