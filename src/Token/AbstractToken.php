<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Token;

use GildasQ\Fpp\Token;

abstract class AbstractToken implements Token
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return sprintf('%s(%s)', static::class, $this->value);
    }
}
