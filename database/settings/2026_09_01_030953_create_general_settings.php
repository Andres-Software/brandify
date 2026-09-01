<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.logo_path', null);
        $this->migrator->add('general.company_name', 'Brandify');
        $this->migrator->add('general.show_topbar', true);
        $this->migrator->add('general.slug_mode', 'auto_title');
        $this->migrator->add('general.slug_prefix', null);
    }
};
