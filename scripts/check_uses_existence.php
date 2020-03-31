<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';
$usesList = fopen($argv[1], 'r');

$errorsCount = 0;
$usesCount = 0;

while ($use = fgets($usesList)) {
    $useInfo = explode('#', $use);

    $lineLocation = $useInfo[0];
    $useValue = trim($useInfo[1]);

    if (!class_exists($useValue)) {
        if (!interface_exists($useValue)) {
            if (!trait_exists($useValue)) {
                $errorsCount++;
                printf("Unable to find class or interface or trait %s (defined in %s)\n", $useValue, $lineLocation);
            }
        }
    }
    $usesCount++;
}

fclose($usesList);

printf("%s 'use' analyzed. %s errors found.\n", $usesCount, $errorsCount);

if ($errorsCount > 0) {
    exit(1);
}

exit(0);
