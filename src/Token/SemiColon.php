<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Token;

use GildasQ\Fpp\Token\AbstractToken;
use GildasQ\Fpp\Lexer;

final class SemiColon extends AbstractToken
{
    public function nextTokenChecker(): Lexer\TokenChecker
    {
        return Lexer\NextToken::any();
    }
}
