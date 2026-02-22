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

// ─── Commands to run ─────────────────────────────────────────────────────────
$commands = [
    'Navigate to project root' => "cd {$projectRoot}",
    'Composer Install'         => "cd {$projectRoot} && composer install --no-dev --optimize-autoloader --no-interaction 2>&1",
    'Config Cache'             => "cd {$projectRoot} && {$phpBin} artisan config:cache 2>&1",
    'Route Cache'              => "cd {$projectRoot} && {$phpBin} artisan route:cache 2>&1",
    'View Cache'               => "cd {$projectRoot} && {$phpBin} artisan view:cache 2>&1",
    'Storage Link'             => "cd {$projectRoot} && {$phpBin} artisan storage:link 2>&1",
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🚀 Deploy — Wagonow</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0d1117; color: #e6edf3; font-family: monospace; padding: 20px; }
        h1 { color: #58a6ff; margin-bottom: 20px; font-size: 20px; }
        .step { background: #161b22; border: 1px solid #30363d; border-radius: 8px; margin-bottom: 16px; overflow: hidden; }
        .step-title { padding: 10px 16px; background: #21262d; color: #8b949e; font-size: 13px; font-weight: bold; }
        .step-title.ok   { border-left: 4px solid #3fb950; color: #3fb950; }
        .step-title.fail { border-left: 4px solid #f85149; color: #f85149; }
        .step-body { padding: 12px 16px; font-size: 13px; line-height: 1.6; white-space: pre-wrap; }
        .done { margin-top: 20px; padding: 16px; background: #0d2a1a; border: 1px solid #3fb950; border-radius: 8px; color: #3fb950; font-size: 16px; }
        .warn { margin-top: 20px; padding: 16px; background: #2a1a0d; border: 1px solid #f85149; border-radius: 8px; color: #f85149; font-size: 14px; }
    </style>
</head>
<body>
    <h1>🚀 Deployment Script — <?= date('Y-m-d H:i:s') ?></h1>
    <p style="color:#8b949e;margin-bottom:20px;">Project root: <code><?= htmlspecialchars($projectRoot) ?></code></p>

<?php
$allOk = true;

// Skip "Navigate" — it's just informational
unset($commands['Navigate to project root']);

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
    ob_flush();
}

if ($allOk): ?>
    <div class="done">✅ All commands completed successfully! Your app should be working now.<br><br>⚠️ <strong>DELETE deploy.php from your server when done!</strong></div>
<?php else: ?>
    <div class="warn">❌ Some commands failed. Check the output above for details.</div>
<?php endif; ?>

</body>
</html>
