<?php

// script used for maintaining versions.json (file referencing all deployed branch documentation on pim-docs)
$branch = $argv[1];
$versionFile = $argv[2];
echo "Version file ${versionFile}";

$fileContent = file_get_contents($versionFile) ?: "[]";
$versions = json_decode($fileContent, true);

$newVersionLabel = versionLabelFromBranchName($branch);
if (containsVersionLabel($versions, $newVersionLabel)) {
    writeReorderedVersions($versions, $versionFile);
    echo ("Existing branch $branch in $versionFile, exiting...");
    exit(0);
}

$versions[] = [
    'label' => $newVersionLabel,
    'url' => "/$branch/index.html"
];

writeReorderedVersions($versions, $versionFile);

function containsVersionLabel(array $versions, string $versionLabel): bool
{
    foreach ($versions as $version) {
        if ($versionLabel === $version['label']) {
            return true;
        }
    }

    return false;
}

function versionLabelFromBranchName(string $branchName): string
{
    return $branchName == "master" ? $branchName : "v${branchName}";
}

function writeReorderedVersions(array $versions, string $versionFile): void
{
    $versions = orderVersions($versions);
    file_put_contents("$versionFile", json_encode($versions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function orderVersions(array $versions): array
{
    usort($versions, static function (array $versionA, array $versionB) {
        if ($versionA['label'] === "master") return -1;
        if ($versionB['label'] === "master") return 1;

        return $versionB <=> $versionA;
    });

    return $versions;
}
