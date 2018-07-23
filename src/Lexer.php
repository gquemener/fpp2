<?php

declare(strict_types=1);

namespace GildasQ\Fpp;

use GildasQ\Fpp\Token;

final class Lexer
{
    public function analyse(string $input)
    {
        $words = array_filter(array_map('trim', preg_split('/([ ;,(){}])/', $input, -1, PREG_SPLIT_DELIM_CAPTURE)));

        return array_map([$this, 'tokenize'], $words);
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

            case '(':
                $class = Token\BeginList::class;
                break;

            case ')':
                $class = Token\EndList::class;
                break;

            case 1 === preg_match('/^(?:string|int|double|float|bool)(?:\[\])?$/', $word):
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

            case 'deriving':
                $class = Token\Deriving::class;
                break;

            default:
                $class = Token\Other::class;
                break;
        }

        return new $class($word);
    }
}
