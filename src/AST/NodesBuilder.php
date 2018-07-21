<?php

declare(strict_types=1);

namespace GildasQ\Fpp\AST;

use GildasQ\Fpp\Token;
use GildasQ\Fpp\AST\Node\NamespaceNode;
use PhpParser\Builder;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;

final class NodesBuilder
{
    private $nodes = [];
    private $currentStatementType;
    private $currentNamespaceNode;
    private $ready = false;

    public function add(Token $token): void
    {
        $method = sprintf('with%sToken', array_reverse(explode('\\', get_class($token)))[0]);
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot add token "%s" because method "GildasQ\Fpp\AST\NodesBuilder::%s" does not exist',
                get_class($token),
                $method
            ));
        }

        $this->{$method}($token);
    }

    public function ready(): bool
    {
        return $this->ready;
    }

    public function nodes(): array
    {
        $nodes = $this->nodes;
        $this->nodes = [];
        $this->ready = false;

        return $nodes;
    }

    private function withStatementTypeToken(Token\StatementType $token): void
    {
        $this->currentStatementType = $token->value();
    }

    private function withOtherToken(Token\Other $token): void
    {
        switch ($this->currentStatementType) {
            case 'namespace':
                $this->currentNamespace = new Builder\Namespace_($token->value());
                break;

            case 'data':
                $this->currentClass = new Builder\Class_($token->value());

            default:
                break;
        }
    }

    private function withBeginArgumentsListToken(Token\BeginArgumentsList $token): void
    {
        $this->arguments = [];
    }

    private function withTypeHintToken(Token\TypeHint $token): void
    {
        $this->properties[] = [
            'type' => $token->value(),
            'name' => null,
        ];
    }

    private function withVariableNameToken(Token\VariableName $token): void
    {
        $this->properties[count($this->properties) - 1]['name'] = $token->value();
    }

    private function withEndArgumentsListToken(Token\EndArgumentsList $token): void
    {
        $this->currentClass->addStmt(
            (new Builder\Method('__construct'))
                ->makePublic()
                ->addParams(array_map(function(array $property): Builder\Param {
                    return (new Builder\Param($property['name']))
                        ->setTypeHint($property['type']);
                }, $this->properties))
                ->addStmts(array_map(function(array $property): Stmt\Expression {
                    return new Stmt\Expression(
                        new Expr\Assign(
                            new Expr\PropertyFetch(new Expr\Variable('this'), $property['name']),
                            new Expr\Variable($property['name'])
                        )
                    );
                }, $this->properties))
        );

        foreach ($this->properties as $argument) {
            $this->currentClass->addStmts([
                (new Builder\Property($argument['name']))->makePrivate(),
                (new Builder\Method($argument['name']))
                    ->makePublic()
                    ->setReturnType($argument['type'])
                    ->addStmt(new Stmt\Return_(new Expr\PropertyFetch(new Expr\Variable('this'), $argument['name'])))
            ]);
        }

        $this->properties = [];
    }

    private function withCommaToken(Token\Comma $token): void
    {
    }

    private function withAssignmentToken(Token\Assignment $token): void
    {
    }

    public function withSemiColonToken(Token\SemiColon $token): void
    {
        if ('data' === $this->currentStatementType) {
            $this->prepareForYielding();
        }
    }

    private function prepareForYielding(): void
    {
        $namespace = clone $this->currentNamespace;
        $namespace->addStmt($this->currentClass);
        $this->nodes = [$namespace->getNode()];
        $this->currentClass = null;
        $this->ready = true;
    }
}
