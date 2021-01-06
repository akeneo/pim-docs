<?php

// script used for maintaining versions.json (file referencing all deployed branch documentation on pim-docs)
$branch = $argv[1];
$versionFile = $argv[2];

$fileContent = file_get_contents($versionFile) ?: "[]";

$json = json_decode($fileContent);

if (containsBranch($json, $branch)) {
    echo ("Existing branch $branch in $versionFile, exiting...");
    exit(0);
}

$json[] = [
    'label' => initLabelFromBranchName($branch),
    'url' => "/$branch/index.html"
];

file_put_contents("$versionFile",
    json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

function containsBranch(array $json, string $branchName): bool
{
    $labelName = initLabelFromBranchName($branchName);
    foreach ($json as $entry) {
        if ($branchName === $entry->label)
            return true;
    }
    return false;
}

function initLabelFromBranchName(string $branchName)
{
    return $branchName == "master" ? $branchName : "v${branchName}";
}
