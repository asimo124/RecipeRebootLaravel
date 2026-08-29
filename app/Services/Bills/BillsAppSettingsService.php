<?php

namespace App\Services\Bills;

use App\Models\Bills\AppSetting;
use Illuminate\Support\Facades\Schema;

class BillsAppSettingsService
{
    public function ensureTable(): void
    {
        $connection = AppSetting::query()->getConnection();

        if (Schema::connection($connection->getName())->hasTable('app_settings')) {
            return;
        }

        $connection->statement("CREATE TABLE IF NOT EXISTS app_settings (
            setting_key VARCHAR(64) NOT NULL PRIMARY KEY,
            setting_value VARCHAR(255) NOT NULL DEFAULT '',
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        AppSetting::query()->firstOrCreate(
            ['setting_key' => 'test_mode'],
            ['setting_value' => '0']
        );
    }

    public function get(string $key, string $default = ''): string
    {
        $this->ensureTable();

        return (string) (AppSetting::query()
            ->where('setting_key', $key)
            ->value('setting_value') ?? $default);
    }

    public function set(string $key, string $value): void
    {
        $this->ensureTable();

        AppSetting::query()->updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }

    public function testModeEnabled(): bool
    {
        return (int) $this->get('test_mode', '0') === 1;
    }
}
