<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Lexer;

use GildasQ\Fpp\Token;

interface TokenChecker
{
    public function comply(Token $token): bool;
}
