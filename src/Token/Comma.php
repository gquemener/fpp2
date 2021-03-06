<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Token;

use GildasQ\Fpp\Token\AbstractToken;
use GildasQ\Fpp\Lexer;

final class Comma extends AbstractToken
{
    public function nextTokenChecker(): Lexer\TokenChecker
    {
        return Lexer\NextToken::instanceOf(
            TypeHint::class,
            Other::class
        );
    }
}
