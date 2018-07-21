<?php

require __DIR__.'/vendor/autoload.php';

use PhpParser\ParserFactory;

$code = <<<CODE
<?php

declare(strict_types=1);

namespace App;

class Foo
{
    public function __construct(string \$name)
    {
        \$this->name = \$name;
    }
}
CODE;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
try {
    $ast = $parser->parse($code);
    die(var_dump($ast[1]->stmts[0]->stmts[0]));
} catch (Error $error) {
    echo "Parse error: {$error->getMessage()}\n";
}

