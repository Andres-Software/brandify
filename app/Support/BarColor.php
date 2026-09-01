<?php

namespace App\Support;

class BarColor
{
    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'emerald' => 'Verde',
            'blue' => 'Azul',
            'violet' => 'Violeta',
            'rose' => 'Rosa',
            'amber' => 'Âmbar',
            'slate' => 'Cinza',
        ];
    }

    public static function hex(string $key): string
    {
        return match ($key) {
            'blue' => '#3b82f6',
            'violet' => '#8b5cf6',
            'rose' => '#f43f5e',
            'amber' => '#f59e0b',
            'slate' => '#64748b',
            default => '#10b981',
        };
    }
}
