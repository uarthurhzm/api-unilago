<?php

function createFileFromStub($type, $name)
{
    $projectRoot = dirname(__DIR__, 2);
    $stubPath = $projectRoot . "/src/Templates/{$type}.stub";

    // Handle special case for Repository
    $folderName = $type === 'Repository' ? 'Repositories' : ucfirst($type) . "s";
    $targetDir = $projectRoot . "/src/" . $folderName;
    $targetPath = "{$targetDir}/{$name}{$type}.php";

    if (!file_exists($stubPath)) {
        echo __DIR__;
        return;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $stub = file_get_contents($stubPath);
    $content = str_replace('{{NAME}}', $name, $stub);

    file_put_contents($targetPath, $content);

    echo ucfirst($type) . " created: {$targetPath}\n";
}

if ($argc < 2) {
    echo "Uso: php make.php NomeDaClasse\n";
    exit(1);
}

$name = $argv[1];

createFileFromStub('Controller', $name);
createFileFromStub('Service', $name);
createFileFromStub('Repository', $name);
