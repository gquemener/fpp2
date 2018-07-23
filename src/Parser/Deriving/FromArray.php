<?php

declare(strict_types=1);

namespace GildasQ\Fpp\Parser\Deriving;

use GildasQ\Fpp\Parser\Deriving;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Builder;

final class FromArray implements Deriving
{
    private $conditions;

    public function beforeTraverse(array $nodes)
    {
        $this->conditions = [];
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Class_) {
            $this->conditions = [];

            return;
        }

        if (!$node instanceof Stmt\Property) {
            return;
        }

        $name = $node->props[0]->name->name;
        $this->conditions[$name] = new Stmt\If_(
            new Expr\BooleanNot(new Expr\FuncCall(new Node\Name('array_key_exists'), [
                new Node\Arg(new Node\Scalar\String_($name)),
                new Node\Arg(new Expr\Variable('data')),
            ])),
            [
                'stmts' => [
                    new Stmt\Throw_(new Expr\New_(
                        new Node\Name\FullyQualified('InvalidArgumentException'),
                        [
                            new Node\Scalar\String_(sprintf('Provided array must contain key "%s"', $name))
                        ]
                    ))
                ]
            ]
        );
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof Stmt\Class_) {
            return;
        }

        $node->stmts[] = (new Builder\Method('fromArray'))
            ->makePublic()
            ->makeStatic()
            ->setReturnType('self')
            ->addParam((new Builder\Param('data'))->setTypeHint('array'))
            ->addStmts(array_merge($this->conditions, [
                new Stmt\Return_(new Expr\New_(
                    new Node\Name('self'),
                    array_map(function(string $name): Node\Arg {
                        return new Node\Arg(new Expr\ArrayDimFetch(
                            new Expr\Variable('data'),
                            new Node\Scalar\String_($name)
                        ));
                    }, array_keys($this->conditions))
                )),
            ]))
            ->getNode();
    }

    public function afterTraverse(array $nodes)
    {
        $this->conditions = [];
    }
}
