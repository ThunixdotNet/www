<?php
declare(strict_types=1);

// Render wiki content using the *same* parser stack as wiki.php,

require __DIR__ . '/../config.php';
require __DIR__ . '/../parsedown-1.7.3/Parsedown.php';
require __DIR__ . '/../parsedown-extra-0.7.1/ParsedownExtra.php';

$page = isset($_GET['page']) ? (string) $_GET['page'] : 'main';

if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_-]*$/', $page)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Bad page name.";
    exit;
}

$contentPath = $doc_root . '/articles/' . $page . '.md';
if (!is_file($contentPath)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Not found.";
    exit;
}

$ParsedownExtra = new ParsedownExtra();

$md = file_get_contents($contentPath);
if ($md === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Failed to read page.";
    exit;
}

$html = $ParsedownExtra->text($md);

if ($page === 'users' || $page === 'server') {
    $inc = $doc_root . '/includes/' . $page . '.php';
    if (is_file($inc)) {
        ob_start();
        require $inc;
        $html .= (string) ob_get_clean();
    }
}

$knownPages = [];
foreach (glob($doc_root . '/articles/*.md') ?: [] as $file) {
    $slug = basename($file, '.md');
    $knownPages[$slug] = true;
}

$rewriteFormActions = function (string $action): string {
    $path = parse_url($action, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        $path = $action;
    }

    $pathLower = strtolower($path);
    if (!preg_match('~(^|/)(includes/(contact|signup)\.php)$~', $pathLower)) {
        return $action;
    }

    $parts = parse_url($action);
    if ($parts === false) {
        if (strpos($action, 'terminal=1') !== false) {
            return $action;
        }
        return (strpos($action, '?') !== false) ? ($action . '&terminal=1') : ($action . '?terminal=1');
    }

    $query = [];
    if (isset($parts['query'])) {
        parse_str((string) $parts['query'], $query);
    }
    $query['terminal'] = '1';
    $queryString = http_build_query($query);

    $rebuilt = '';
    if (isset($parts['scheme'])) {
        $rebuilt .= $parts['scheme'] . '://';
    } elseif (str_starts_with($action, '//')) {
        $rebuilt .= '//';
    }

    if (isset($parts['user'])) {
        $rebuilt .= $parts['user'];
        if (isset($parts['pass'])) {
            $rebuilt .= ':' . $parts['pass'];
        }
        $rebuilt .= '@';
    }

    if (isset($parts['host'])) {
        $rebuilt .= $parts['host'];
    }
    if (isset($parts['port'])) {
        $rebuilt .= ':' . $parts['port'];
    }

    $rebuilt .= $parts['path'] ?? '';
    if ($queryString !== '') {
        $rebuilt .= '?' . $queryString;
    }
    if (isset($parts['fragment'])) {
        $rebuilt .= '#' . $parts['fragment'];
    }

    return $rebuilt;
};


$isTerminalSuccessFormTarget = function (string $action): bool {
    $path = parse_url($action, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        $path = $action;
    }

    $pathLower = strtolower($path);
    return preg_match('~(^|/)(includes/(contact|signup)\.php)$~', $pathLower) === 1;
};


$html = preg_replace_callback(
    '~(<form\b[^>]*\baction\s*=\s*)(["\'])([^"\']+)(\2)~i',
    function (array $m) use ($rewriteFormActions): string {
        $new = $rewriteFormActions((string) $m[3]);
        return $m[1] . $m[2] . $new . $m[4];
    },
    $html
);

$html = preg_replace_callback(
    '~(<form\b[^>]*>)~i',
    function (array $m) use ($rewriteFormActions, $isTerminalSuccessFormTarget): string {
        $tag = $m[1];

        if (preg_match('~\baction\s*=\s*(["\'])([^"\']+)\1~i', $tag, $am) !== 1) {
            return $tag;
        }

        $action = (string) $am[2];
        if ($isTerminalSuccessFormTarget($action) === false) {
            return $tag;
        }

        $newAction = $rewriteFormActions($action);
        $tag = preg_replace(
            '~\baction\s*=\s*(["\'])([^"\']+)\1~i',
            'action=' . $am[1] . $newAction . $am[1],
            $tag,
            1
        );

        return $tag . "\n" . '<input type="hidden" name="terminal" value="1">';
    },
    $html
);

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML(
    '<!doctype html><html><head><meta charset="utf-8"></head><body><div id="content">' . $html . '</div></body></html>',
    LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
);

foreach ($dom->getElementsByTagName('a') as $a) {
    $href = $a->getAttribute('href');
    if ($href === '' || str_starts_with($href, '#')) {
        continue;
    }

    if (preg_match('/^[a-zA-Z][a-zA-Z0-9+.-]*:/', $href) === 1) {
        $a->setAttribute('target', '_blank');
        $a->setAttribute('rel', 'noopener');
        continue;
    }

    if (str_starts_with($href, '/')) {
        $path = parse_url($href, PHP_URL_PATH) ?? '';
        $slug = ltrim($path, '/');

        if ($slug !== '' && isset($knownPages[$slug])) {
            $a->setAttribute('href', './view.php?page=' . rawurlencode($slug));
            continue;
        }

        $a->setAttribute('target', '_blank');
        $a->setAttribute('rel', 'noopener');
        continue;
    }

    $a->setAttribute('target', '_blank');
    $a->setAttribute('rel', 'noopener');
}

foreach ($dom->getElementsByTagName('form') as $form) {
    $action = $form->getAttribute('action');
    if ($action !== '') {
        $form->setAttribute('action', $rewriteFormActions($action));
    }
}

$finalHtml = $dom->saveHTML();
libxml_clear_errors();

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Content-Type: text/html; charset=UTF-8');

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($site_name . ' - ' . $page, ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($site_root . '/includes/terminal.css', ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
<?php
$dom2 = new DOMDocument();
libxml_use_internal_errors(true);
$dom2->loadHTML($finalHtml);
libxml_clear_errors();
$content = $dom2->getElementById('content');
if ($content === null) {
    echo $finalHtml;
} else {
    echo $dom2->saveHTML($content);
}
?>
</body>
</html>
