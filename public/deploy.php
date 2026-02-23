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
$phpBin      = '/usr/local/bin/lsphp'; // Use explicit path found in diagnostics

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🚀 Robust Deploy — Wagonow</title>
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
    <h1>🚀 Robust Deployment Script — <?= date('Y-m-d H:i:s') ?></h1>

    <div class="info">
        <strong>Environment Info:</strong><br>
        PHP User: <?= get_current_user() ?><br>
        Project Root: <?= htmlspecialchars($projectRoot) ?><br>
        Current Dir: <?= htmlspecialchars(__DIR__) ?><br>
        PHP Binary: <?= htmlspecialchars($phpBin) ?><br>
        Exec/Shell_Exec: <?= (function_exists('exec') && function_exists('shell_exec')) ? '✅ Enabled' : '❌ Disabled' ?><br>
    </div>

<?php
if (!function_exists('exec')) {
    die("<div class='warn'>❌ ERROR: PHP 'exec' function is disabled.</div>");
}

// ─── Helper Functions ─────────────────────────────────────────────────────────

function run_cmd($label, $cmd) {
    echo "<div class='step'>";
    echo "<div class='step-title'>⏳ Running: {$label}...</div>";
    flush(); if (ob_get_level() > 0) ob_flush();

    $output = [];
    $exitCode = 0;
    exec($cmd, $output, $exitCode);

    $ok     = ($exitCode === 0);
    $status = $ok ? 'ok' : 'fail';
    $icon   = $ok ? '✅' : '❌';
    $text   = implode("\n", $output) ?: '(no output)';

    // Update title with status
    echo "<script>document.querySelectorAll('.step-title').item(document.querySelectorAll('.step-title').length - 1).className = 'step-title {$status}';</script>";
    echo "<script>document.querySelectorAll('.step-title').item(document.querySelectorAll('.step-title').length - 1).innerHTML = '{$icon} {$label} (exit: {$exitCode})';</script>";
    
    echo "<div class='step-body'>" . htmlspecialchars($text) . "</div>";
    echo "</div>";
    
    flush(); if (ob_get_level() > 0) ob_flush();
    return $ok;
}

// ─── Execution ───────────────────────────────────────────────────────────────

$allOk = true;

// 1. Check if global composer exists
$hasGlobalComposer = false;
exec("composer --version", $trash, $exitCode);
if ($exitCode === 0) {
    $hasGlobalComposer = true;
    $composerCmd = "composer";
} else {
    // 2. Try to download composer.phar if not present
    $composerPath = $projectRoot . '/composer.phar';
    if (!file_exists($composerPath)) {
        $allOk = $allOk && run_cmd("Download composer.phar", "cd {$projectRoot} && curl -sS https://getcomposer.org/installer | {$phpBin}");
    }
    $composerCmd = "{$phpBin} {$composerPath}";
}

// 3. Run Commands
$allOk = $allOk && run_cmd("Composer Install", "cd {$projectRoot} && {$composerCmd} install --no-dev --optimize-autoloader --no-interaction 2>&1");
$allOk = $allOk && run_cmd("Artisan Config Cache", "cd {$projectRoot} && {$phpBin} artisan config:cache 2>&1");
$allOk = $allOk && run_cmd("Artisan Route Cache", "cd {$projectRoot} && {$phpBin} artisan route:cache 2>&1");
$allOk = $allOk && run_cmd("Artisan View Cache", "cd {$projectRoot} && {$phpBin} artisan view:cache 2>&1");

if ($allOk): ?>
    <div class="done">✅ All commands completed successfully! Your app should be working now.</div>
<?php else: ?>
    <div class="warn">❌ Some commands failed.</div>
<?php endif; ?>

</body>
</html>


