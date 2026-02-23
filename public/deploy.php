<?php

/**
 * Secure Deployment Script
 * URL: https://app.wagonow.com/deploy.php?token=YOUR_SECRET_TOKEN
 *
 * ⚠️  DELETE THIS FILE after deployment is fixed!
 */

// ─── Secret Token ─────────────────────────────────────────────────────────────
define('DEPLOY_TOKEN', 'wagonow_deploy_2024_secret');

// ─── Validate Token ────────────────────────────────────────────────────────────
$token = $_GET['token'] ?? '';
if ($token !== DEPLOY_TOKEN) {
    http_response_code(403);
    die('<h2 style="color:red;font-family:monospace;">403 Forbidden — Invalid token</h2>');
}

// ─── Config ──────────────────────────────────────────────────────────────────
$projectRoot = dirname(__DIR__); // /home/wagonow/public_html
$phpBin      = PHP_BINARY ?: 'php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🚀 Debug Deploy — Wagonow</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0d1117; color: #e6edf3; font-family: monospace; padding: 20px; }
        h1 { color: #58a6ff; margin-bottom: 20px; font-size: 20px; }
        .step { background: #161b22; border: 1px solid #30363d; border-radius: 8px; margin-bottom: 16px; overflow: hidden; }
        .step-title { padding: 10px 16px; background: #21262d; color: #8b949e; font-size: 13px; font-weight: bold; }
        .step-title.ok   { border-left: 4px solid #3fb950; color: #3fb950; }
        .step-title.fail { border-left: 4px solid #f85149; color: #f85149; }
        .step-body { padding: 12px 16px; font-size: 13px; line-height: 1.6; white-space: pre-wrap; }
        .info { background: #1c2128; border: 1px solid #444c56; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .done { margin-top: 20px; padding: 16px; background: #0d2a1a; border: 1px solid #3fb950; border-radius: 8px; color: #3fb950; font-size: 16px; }
        .warn { margin-top: 20px; padding: 16px; background: #2a1a0d; border: 1px solid #f85149; border-radius: 8px; color: #f85149; font-size: 14px; }
    </style>
</head>
<body>
    <h1>🚀 Debug Deployment Script — <?= date('Y-m-d H:i:s') ?></h1>

    <div class="info">
        <strong>Environment Info:</strong><br>
        PHP User: <?= get_current_user() ?><br>
        Project Root: <?= htmlspecialchars($projectRoot) ?><br>
        Current Dir: <?= htmlspecialchars(__DIR__) ?><br>
        PHP Binary: <?= htmlspecialchars($phpBin) ?><br>
        Exec Enabled: <?= function_exists('exec') ? '✅ Yes' : '❌ No' ?><br>
        Shell_Exec Enabled: <?= function_exists('shell_exec') ? '✅ Yes' : '❌ No' ?><br>
        Composer.json: <?= file_exists($projectRoot . '/composer.json') ? '✅ Found' : '❌ Missing' ?><br>
        Vendor Dir: <?= is_dir($projectRoot . '/vendor') ? '✅ Found' : '❌ Missing' ?><br>
        Storage Dir: <?= is_dir($projectRoot . '/storage') ? '✅ Found' : '❌ Missing' ?><br>
    </div>

<?php
if (!function_exists('exec')) {
    echo "<div class='warn'>❌ ERROR: PHP 'exec' function is disabled. Deployment cannot proceed.</div>";
} else {
    // ─── Commands to run ─────────────────────────────────────────────────────────
    $commands = [
        'List Files'               => "ls -lah {$projectRoot}",
        'Check Composer'           => "composer --version 2>&1",
        'Composer Install'         => "cd {$projectRoot} && composer install --no-dev --optimize-autoloader --no-interaction 2>&1",
        'Artisan Version'          => "cd {$projectRoot} && {$phpBin} artisan --version 2>&1",
        'Config Cache'             => "cd {$projectRoot} && {$phpBin} artisan config:cache 2>&1",
        'Route Cache'              => "cd {$projectRoot} && {$phpBin} artisan route:cache 2>&1",
        'View Cache'               => "cd {$projectRoot} && {$phpBin} artisan view:cache 2>&1",
    ];

    $allOk = true;

    foreach ($commands as $label => $cmd) {
        $output = [];
        $exitCode = 0;

        exec($cmd, $output, $exitCode);

        $ok     = ($exitCode === 0);
        $allOk  = $allOk && $ok;
        $status = $ok ? 'ok' : 'fail';
        $icon   = $ok ? '✅' : '❌';
        $text   = implode("\n", $output) ?: '(no output)';

        echo "<div class='step'>";
        echo "<div class='step-title {$status}'>{$icon} {$label} (exit: {$exitCode})</div>";
        echo "<div class='step-body'>" . htmlspecialchars($text) . "</div>";
        echo "</div>";
        flush();
        if (ob_get_level() > 0) ob_flush();
    }

    if ($allOk): ?>
        <div class="done">✅ All commands completed successfully!</div>
    <?php else: ?>
        <div class="warn">❌ Some commands failed.</div>
    <?php endif;
}
?>

</body>
</html>

