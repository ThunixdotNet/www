<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$rootDir = dirname(__DIR__, 2);
if (!is_dir($rootDir)) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid root directory'], JSON_UNESCAPED_SLASHES);
    exit;
}

function json_out(array $data, int $status = 200): void
{
    http_response_code($status);
    try {
        echo json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    } catch (Throwable $e) {
        http_response_code(500);
        echo '{"error":"JSON encoding failed"}';
    }
    exit;
}
