<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$siteRoot = '//' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

$skelIndex = '/etc/skel/public_html/index.html';
$skelIndexCksum = is_file($skelIndex) ? @md5_file($skelIndex) : null;

$users = [];
$homes = glob('/home/*', GLOB_ONLYDIR) ?: [];

foreach ($homes as $homeDir) {
    $user = basename($homeDir);
    if ($user === '' || $user === 'lost+found') {
        continue;
    }

    $userIndex = $homeDir . '/public_html/index.html';
    $userPub = $homeDir . '/public_html';

    if (!is_dir($userPub)) {
        continue;
    }

    $hasCustomIndex = false;
    if (is_file($userIndex) && $skelIndexCksum !== null) {
        $userCksum = @md5_file($userIndex);
        $hasCustomIndex = ($userCksum !== false && $userCksum !== $skelIndexCksum);
    } elseif (is_file($userIndex)) {
        $hasCustomIndex = true;
    }

    $users[] = [
        'username' => $user,
        'url' => $siteRoot . '/~' . rawurlencode($user) . '/',
        'hasContent' => $hasCustomIndex,
    ];
}

json_out(['users' => $users]);
