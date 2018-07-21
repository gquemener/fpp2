<?php

declare(strict_types=1);

namespace GildasQ\Fpp;

use GildasQ\Fpp\Token;
use GildasQ\Fpp\Lexer\NextToken;
use GildasQ\Fpp\AST\NodesBuilder;

final class Parser
{
    private $lexer;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    public function parse(string $input)
    {
        $nodes = [];
        $builder = new NodesBuilder();
        $tokens = $this->lexer->analyse($input);

        $statementStarts = true;
        foreach ($tokens as $token) {
            if ($statementStarts) {
                $checker = NextToken::instanceof(
                    Token\StatementType::class
                );
                $statementStarts = false;
            }
            if (!$checker->comply($token)) {
                throw new \RuntimeException(sprintf(
                    'Unexpected token "%s"',
                    $token
                ));
            }

            $builder->add($token);

            if ($builder->ready()) {
                yield $builder->nodes();
            }

            $checker = $token->nextTokenChecker();
        }
    }
}
