<?php

namespace JeroenGerits\Support\Helpers;

function dd(...$vars): void
{
    foreach ($vars as $var) {
        var_dump($var);
    }
    exit(1);
}
