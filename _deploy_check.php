<?php
/**
 * TEMPORARY deploy helper (placed in public/) - delete after use.
 * GET /_deploy_check.php?key=<secret>[&run=1]
 *   no run=1 -> read-only: migrate status + column presence
 *   run=1    -> runs `migrate --force` then clears caches
 */
$secret = 'opow_deploy_2026_x7k2';
if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403);
    die('Forbidden');
}

$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain; charset=utf-8');

echo "=== ENV: " . config('app.env') . " | DEBUG=" . var_export(config('app.debug'), true) . " ===\n\n";

echo "=== MIGRATE STATUS ===\n";
$kernel->call('migrate:status');

if (($_GET['run'] ?? '') === '1') {
    echo "\n=== RUNNING migrate --force ===\n";
    $kernel->call('migrate', ['--force' => true]);

    echo "\n=== CLEARING CACHES ===\n";
    foreach (['cache:clear', 'view:clear', 'route:clear'] as $cmd) {
        $kernel->call($cmd);
        echo "  $cmd ok\n";
    }

    echo "\n=== POST-CHECK ===\n";
    $kernel->call('migrate:status');

    echo "\ntickets.scanned_at: " . (Schema::hasColumn('tickets', 'scanned_at') ? 'YES' : 'NO') . "\n";
    echo "tickets.scanned_by: " . (Schema::hasColumn('tickets', 'scanned_by') ? 'YES' : 'NO') . "\n";
    echo "complexes.allow_time_conflict: " . (Schema::hasColumn('complexes', 'allow_time_conflict') ? 'YES' : 'NO') . "\n";
    echo "clubs.activity_id: " . (Schema::hasColumn('clubs', 'activity_id') ? 'YES' : 'NO') . "\n";

    echo "\n=== DONE ===\n";
} else {
    echo "\n=== column presence ===\n";
    echo "tickets.scanned_at: " . (Schema::hasColumn('tickets', 'scanned_at') ? 'YES' : 'NO') . "\n";
    echo "tickets.scanned_by: " . (Schema::hasColumn('tickets', 'scanned_by') ? 'YES' : 'NO') . "\n";
    echo "complexes.allow_time_conflict: " . (Schema::hasColumn('complexes', 'allow_time_conflict') ? 'YES' : 'NO') . "\n";
    echo "clubs.activity_id: " . (Schema::hasColumn('clubs', 'activity_id') ? 'YES' : 'NO') . "\n";
    echo "\n(read-only mode - pass run=1 to migrate)\n";
}
