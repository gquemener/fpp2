<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Parser\Deriving;

use GildasQ\Fpp\Parser\Deriving;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Builder;

final class ToArray implements Deriving
{
    private $arrayStmt;

    public function beforeTraverse(array $nodes)
    {
        $this->arrayStmt = null;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Class_) {
            $this->arrayStmt = new Expr\Array_();

            return;
        }

        if (!$node instanceof Stmt\Property) {
            return;
        }

        $name = $node->props[0]->name->name;
        $this->arrayStmt->items[] = new Expr\ArrayItem(
            new Expr\PropertyFetch(new Expr\Variable('this'), $name),
            new Node\Scalar\String_($name)
        );
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof Stmt\Class_) {
            return;
        }

        $node->stmts[] = (new Builder\Method('toArray'))
            ->makePublic()
            ->setReturnType('array')
            ->addStmt(new Stmt\Return_($this->arrayStmt))
            ->getNode();
    }

    public function afterTraverse(array $nodes)
    {
        $this->arrayStmt = null;
    }
}
