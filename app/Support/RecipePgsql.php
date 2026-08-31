<?php

namespace App\Support;

use App\Models\RecipeModel;
use RuntimeException;

class RecipePgsql
{
    public static function assertConfigured(): void
    {
        if (! extension_loaded('pdo_pgsql')) {
            throw new RuntimeException(
                'PHP pdo_pgsql is not loaded. On Debian: sudo apt install php-pgsql && sudo systemctl restart php8.2-fpm'
            );
        }

        $pgsql = config('database.connections.'.RecipeModel::CONNECTION_NAME);
        $mysql = config('database.connections.mysql');

        if (! is_array($pgsql) || ($pgsql['driver'] ?? null) !== 'pgsql') {
            throw new RuntimeException(
                'The pgsql connection is missing from config. Deploy the updated config/database.php, set PGSQL_* in .env, then php artisan config:clear'
            );
        }

        $host = (string) ($pgsql['host'] ?? '');
        $port = (int) ($pgsql['port'] ?? 0);
        $mysqlHost = (string) ($mysql['host'] ?? '');
        $mysqlPort = (int) ($mysql['port'] ?? 3306);

        if ($port === 3306 || ($host !== '' && $host === $mysqlHost && $port === $mysqlPort)) {
            throw new RuntimeException(
                "The pgsql connection is pointing at {$host}:{$port} (MySQL). "
                .'Set PGSQL_HOST, PGSQL_PORT=5432, PGSQL_DATABASE, PGSQL_USERNAME, PGSQL_PASSWORD in .env, '
                .'then run `php artisan config:clear`. If you use config caching, run `php artisan config:cache` after that.'
            );
        }
    }
}
