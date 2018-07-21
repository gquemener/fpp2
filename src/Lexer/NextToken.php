<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Lexer;

use GildasQ\Fpp\Token;

final class NextToken
{
    public static function any(): TokenChecker
    {
        return new class implements TokenChecker
        {
            public function comply(Token $token): bool
            {
                return true;
            }
        };
    }

    public static function none(): TokenChecker
    {
        return new class implements TokenChecker
        {
            public function comply(Token $token): bool
            {
                return false;
            }
        };
    }

    public static function instanceOf(string ...$classes): TokenChecker
    {
        return new class($classes) implements TokenChecker
        {
            private $classes;

            public function __construct(array $classes)
            {
                $this->classes = $classes;
            }

            public function comply(Token $token): bool
            {
                foreach ($this->classes as $class) {
                    if ($token instanceof $class) {
                        return true;
                    }
                }

                return false;
            }
        };
    }
}
