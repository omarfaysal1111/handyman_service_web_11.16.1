<?php

/**
 * Secure Deployment Script - v5 Robust
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
$projectRoot = dirname(__DIR__);
$phpBin      = '/usr/local/bin/lsphp'; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🚀 Final Fix Deploy — Wagonow</title>
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
    <h1>🚀 Final Deployment Fix — <?= date('Y-m-d H:i:s') ?></h1>

    <div class="info">
        <strong>Environment Info:</strong><br>
        PHP User: <?= get_current_user() ?><br>
        Project Root: <?= htmlspecialchars($projectRoot) ?><br>
        PHP Binary: <?= htmlspecialchars($phpBin) ?><br>
    </div>

<?php
if (!function_exists('exec')) {
    die("<div class='warn'>❌ ERROR: PHP 'exec' function is disabled.</div>");
}

function run_cmd($label, $cmd) {
    echo "<div class='step'>";
    echo "<div class='step-title'>⏳ Running: {$label}...</div>";
    flush(); if (ob_get_level() > 0) ob_flush();

    $output = [];
    $exitCode = 0;
    exec($cmd . " 2>&1", $output, $exitCode);

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

// 1. Clear bootstrap/cache (prevents 'Class not found' from cached providers)
run_cmd("Clear Bootstrap Cache", "rm -f {$projectRoot}/bootstrap/cache/*.php");

// 2. Setup Composer
$composerPath = $projectRoot . '/composer.phar';
if (!file_exists($composerPath)) {
    run_cmd("Download composer.phar", "cd {$projectRoot} && curl -sS https://getcomposer.org/installer | {$phpBin}");
}
$composerCmd = "{$phpBin} {$composerPath}";

// 3. Main Installation
$allOk = $allOk && run_cmd("Composer Install (No Dev)", "cd {$projectRoot} && {$composerCmd} install --no-dev --optimize-autoloader --no-interaction");

// 4. Artisan Tasks
if (file_exists($projectRoot . '/artisan')) {
    run_cmd("Artisan Config Cache", "cd {$projectRoot} && {$phpBin} artisan config:cache");
    run_cmd("Artisan Route Cache", "cd {$projectRoot} && {$phpBin} artisan route:cache");
    run_cmd("Artisan View Cache", "cd {$projectRoot} && {$phpBin} artisan view:cache");
}

if ($allOk): ?>
    <div class="done">✅ Final Fix Complete! Refresh your home page now.</div>
<?php else: ?>
    <div class="warn">❌ Installation failed. Check composer error above.</div>
<?php endif; ?>

</body>
</html>




