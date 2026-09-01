<?php

namespace App\Services;

use App\Models\Proposal;
use App\Settings\GeneralSettings;
use Illuminate\Support\Str;

class SlugGenerator
{
    public function __construct(
        protected GeneralSettings $settings,
    ) {}

    /**
     * Suggest a slug based on the configured slug generation mode.
     */
    public function suggest(?string $title): string
    {
        return match ($this->settings->slug_mode) {
            'auto_title' => filled($title) ? Str::slug($title) : $this->randomSlug(),
            'random' => $this->randomSlug(),
            'prefix' => $this->prefixedSlug(),
            'manual' => '',
            default => $this->randomSlug(),
        };
    }

    /**
     * Ensure the given slug is unique among proposals, appending an incremental
     * numeric suffix (-1, -2, ...) until a free slug is found.
     */
    public function ensureUnique(string $base, ?int $ignoreId = null): string
    {
        $slug = $base;
        $suffix = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Proposal::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }

    protected function randomSlug(): string
    {
        return Str::lower(Str::random(8));
    }

    protected function prefixedSlug(): string
    {
        return "{$this->settings->slug_prefix}-".Str::lower(Str::random(6));
    }
}
