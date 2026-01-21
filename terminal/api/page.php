<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$p = $_GET['p'] ?? '';
if (!is_string($p)) {
    json_out(['error' => 'Invalid page'], 400);
}

$slug = trim($p);
$slug = ltrim($slug, '/');
$slug = preg_replace('/\?.*$/', '', $slug) ?? $slug;

if ($slug === '' || !preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]*$/', $slug)) {
    json_out(['error' => 'Invalid page'], 400);
}

if (preg_match('/^success\d+$/', $slug) === 1) {
    json_out(['error' => 'Not found'], 404);
}

$file = $rootDir . '/articles/' . $slug . '.md';
if (!is_file($file)) {
    json_out(['error' => 'Not found'], 404);
}

$md = file_get_contents($file);
if ($md === false) {
    json_out(['error' => 'Failed to read page'], 500);
}

$parsedownPath = $rootDir . '/parsedown-1.7.3/Parsedown.php';
$extraPath = $rootDir . '/parsedown-extra-0.7.1/ParsedownExtra.php';

if (is_file($parsedownPath)) {
    require_once $parsedownPath;
}
if (is_file($extraPath)) {
    require_once $extraPath;
}

$html = '';
if (class_exists('ParsedownExtra')) {
    $pd = new ParsedownExtra();
    $html = $pd->text($md);
}

json_out([
    'slug' => $slug,
    'markdown' => $md,
    'html' => $html,
]);
