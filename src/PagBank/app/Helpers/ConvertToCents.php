<?php

namespace Helpers;

class ConvertToCents
{
    public static function run(string $value): int
    {
        $cleanValue = preg_replace('/[^\d,\.]/', '', $value);
        $valueWithDot = str_replace(',', '.', $cleanValue);

        if (strpos($valueWithDot, '.') === false) {
            $valueWithDot .= '.00';
        }

        $floatValue = (float) $valueWithDot;
        $centsValue = intval(round($floatValue * 100));

        return $centsValue;
    }
}
