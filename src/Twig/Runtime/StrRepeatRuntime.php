<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class StrRepeatRuntime implements RuntimeExtensionInterface
{
    public function repeatString(int $count, string $str)
    {
        return str_repeat($str, $count);
    }

    public function strRepeat(string $str, int $count)
    {
        return str_repeat($str, $count);
    }
}
