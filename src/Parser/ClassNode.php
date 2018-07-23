<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Parser;

use PhpParser\NodeVisitor;

final class ClassNode
{
    private $ast;
    private $visitors;

    public function __construct(array $ast, array $visitors)
    {
        $this->ast = $ast;
        $this->visitors = $visitors;
    }

    public function ast(): array
    {
        return $this->ast;
    }

    public function visitors(): array
    {
        return $this->visitors;
    }
}
