<?php

namespace Helpers;

class ContainsKeys
{
    public static function run(array $array, array $keys): bool
    {
        foreach ($array as $item) {
            if (is_array($item)) {
                $hasAllKeys = true;
                foreach ($keys as $key => $value) {
                    if (!array_key_exists($key, $item)) {
                        $hasAllKeys = false;
                        break;
                    }
                }
                if ($hasAllKeys) {
                    return true;
                }
            }
        }
        return false;
    }
}
