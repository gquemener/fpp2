<?php
require __DIR__.'/vendor/autoload.php';

use GildasQ\Fpp\Lexer;
use GildasQ\Fpp\Parser;
use PhpParser\NodeDumper;

$lexer = new Lexer();
$parser = new Parser($lexer);

$input = <<<FPP
namespace App;
data Money = Money { int \$amount, string \$currency };
data InvoiceLine { Money \$total };
FPP;

printf(PHP_EOL . '/******* INPUT ***********/' . PHP_EOL);
echo $input . PHP_EOL;

$prettyPrinter = new PhpParser\PrettyPrinter\Standard(['shortArraySyntax' => true]);
foreach ($parser->parse($input) as $i => $nodes) {
    printf(PHP_EOL . '/******* FILE %d *********/' . PHP_EOL, $i + 1);
    echo $prettyPrinter->prettyPrintFile($nodes) . PHP_EOL;
}
