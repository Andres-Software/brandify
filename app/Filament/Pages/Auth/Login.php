<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static bool $isDiscovered = false;

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }
}
