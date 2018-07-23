<?php
require __DIR__.'/vendor/autoload.php';

use GildasQ\Fpp\Lexer;
use GildasQ\Fpp\Parser;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;

$lexer = new Lexer();
$parser = new Parser($lexer);

$input = <<<FPP
namespace App;
data Sample = Sample { int[] \$foo, double \$bar, bool \$baz } deriving (ToArray, FromArray);
FPP;

printf(PHP_EOL . '/******* INPUT ***********/' . PHP_EOL);
echo $input . PHP_EOL;

$prettyPrinter = new PhpParser\PrettyPrinter\Standard(['shortArraySyntax' => true]);
foreach ($parser->parse($input) as $i => $nodes) {
    $traverser = new NodeTraverser();
    foreach ($nodes->visitors() as $visitor) {
        $traverser->addVisitor($visitor);
    }

    $ast = $traverser->traverse($nodes->ast());
    printf(PHP_EOL . '/******* FILE %d *********/' . PHP_EOL, $i + 1);
    echo $prettyPrinter->prettyPrintFile($ast) . PHP_EOL;
}
