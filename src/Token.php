<?php

declare(strict_types=1);

namespace GildasQ\Fpp;

use GildasQ\Fpp\Lexer\TokenChecker;

interface Token
{
    public function nextTokenChecker(): TokenChecker;
}

