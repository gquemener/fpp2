<?php

declare(strict_types=1);

namespace GildasQ\Fpp;

use GildasQ\Fpp\Token;

final class Lexer
{
    public function analyse(string $input)
    {
        return array_map(
            [$this, 'tokenize'],
            array_filter(array_map(
                'trim',
                preg_split('/([ ;,])/', $input, -1, PREG_SPLIT_DELIM_CAPTURE)
            ))
        );
    }

    private function tokenize(string $word): Token
    {
        $class = null;
        switch ($word) {
            case 'namespace':
            case 'data':
                $class = Token\StatementType::class;
                break;

            case '=':
                $class = Token\Assignment::class;
                break;

            case '{':
                $class = Token\BeginArgumentsList::class;
                break;

            case '}':
                $class = Token\EndArgumentsList::class;
                break;

            case 'string':
            case 'int':
            case 'float':
            case 'bool':
                $class = Token\TypeHint::class;
                break;

            case strpos($word, '$') === 0:
                $word = substr($word, 1);
                $class = Token\VariableName::class;
                break;

            case ',':
                $class = Token\Comma::class;
                break;

            case ';':
                $class = Token\SemiColon::class;
                break;

            default:
                $class = Token\Other::class;
                break;
        }

        return new $class($word);
    }
}
