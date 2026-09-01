<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $logo_path;

    public ?string $company_name;

    public bool $show_topbar;

    public string $bar_color;

    public string $slug_mode;

    public ?string $slug_prefix;

    public static function group(): string
    {
        return 'general';
    }
}
