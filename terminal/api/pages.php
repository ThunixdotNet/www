<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$articlesDir = $rootDir . '/articles';
if (!is_dir($articlesDir)) {
    json_out(['pages' => []]);
}

$pages = [];
$files = glob($articlesDir . '/*.md') ?: [];
sort($files);

foreach ($files as $file) {
    $slug = basename($file, '.md');

    if (preg_match('/^success\d+$/', $slug) === 1) {
        continue;
    }

    $title = $slug;

    $fh = fopen($file, 'rb');
    if ($fh !== false) {
        for ($i = 0; $i < 20; $i++) {
            $line = fgets($fh);
            if ($line === false) {
                break;
            }
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^#\s+(.+)$/', $line, $m)) {
                $title = trim($m[1]);
                break;
            }
            $title = mb_substr($line, 0, 80);
            break;
        }
        fclose($fh);
    }

    $pages[] = [
        'slug' => $slug,
        'title' => $title,
    ];
}

json_out(['pages' => $pages]);
