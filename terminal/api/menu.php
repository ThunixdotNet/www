<?php
declare(strict_types=1);

require __DIR__ . '/terminal/_bootstrap.php';

$file = $rootDir . '/includes/sidebar.md';
if (!is_file($file)) {
    json_out(['sections' => []]);
}

$md = file_get_contents($file);
if ($md === false) {
    json_out(['sections' => []]);
}

$lines = preg_split('/\r\n|\n|\r/', $md) ?: [];
$sections = [];

$current = null;

foreach ($lines as $line) {
    $raw = rtrim($line);

    // Section header: "-   Title"
    if (preg_match('/^\-\s{2,}(.+)$/', $raw, $m)) {
        if ($current !== null) {
            $sections[] = $current;
        }
        $current = [
            'title' => trim($m[1]),
            'items' => [],
        ];
        continue;
    }

    // Menu item: "    -   [Text](Href)"
    if ($current !== null && preg_match('/^\s+\-\s{2,}\[(.+?)\]\((.+?)\)\s*$/', $raw, $m)) {
        $text = trim($m[1]);
        $href = trim($m[2]);

        $internal = str_starts_with($href, '/');
        $slug = $internal ? ltrim(parse_url($href, PHP_URL_PATH) ?? '', '/') : '';

        $current['items'][] = [
            'text' => $text,
            'href' => $href,
            'internal' => $internal,
            'slug' => $slug,
        ];
        continue;
    }
}

if ($current !== null) {
    $sections[] = $current;
}

json_out(['sections' => $sections]);
