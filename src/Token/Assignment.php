<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Token;

use GildasQ\Fpp\Lexer;
use GildasQ\Fpp\Token\AbstractToken;

final class Assignment extends AbstractToken
{
    public function nextTokenChecker(): Lexer\TokenChecker
    {
        return Lexer\NextToken::any();
    }
}
