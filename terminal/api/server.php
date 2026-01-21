<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$candidates = [
    $rootDir . '/report',
    $rootDir . '/includes/report',
];

$report = null;
foreach ($candidates as $cand) {
    if (is_file($cand)) {
        $report = $cand;
        break;
    }
}

if ($report === null) {
    json_out(['rows' => [], 'lastUpdated' => null]);
}

$rows = [];
$fh = fopen($report, 'rb');
if ($fh === false) {
    json_out(['rows' => [], 'lastUpdated' => null]);
}

while (($line = fgets($fh)) !== false) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $parts = str_getcsv($line);
    if (count($parts) < 3) {
        continue;
    }
    $rows[] = [
        'host' => (string)$parts[0],
        'check' => (string)$parts[1],
        'status' => (string)$parts[2],
    ];
}
fclose($fh);

$mtime = @filemtime($report);
$last = $mtime ? gmdate('c', (int)$mtime) : null;

json_out(['rows' => $rows, 'lastUpdated' => $last]);
