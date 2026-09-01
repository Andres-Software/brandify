<?php

namespace Tests\Unit;

use App\Models\Directory;
use App\Models\Proposal;
use App\Services\SlugGenerator;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_title_mode_slugifies_the_title(): void
    {
        app(GeneralSettings::class)->slug_mode = 'auto_title';

        $slug = app(SlugGenerator::class)->suggest('O Fim da Burocracia Manual');

        $this->assertSame('o-fim-da-burocracia-manual', $slug);
    }

    public function test_auto_title_mode_falls_back_to_random_when_title_is_empty(): void
    {
        app(GeneralSettings::class)->slug_mode = 'auto_title';

        $slug = app(SlugGenerator::class)->suggest(null);

        $this->assertNotEmpty($slug);
        $this->assertSame(8, strlen($slug));
    }

    public function test_random_mode_generates_an_eight_character_lowercase_slug(): void
    {
        app(GeneralSettings::class)->slug_mode = 'random';

        $slug = app(SlugGenerator::class)->suggest('Qualquer título');

        $this->assertSame(8, strlen($slug));
        $this->assertSame(strtolower($slug), $slug);
    }

    public function test_prefix_mode_prepends_the_configured_prefix(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->slug_mode = 'prefix';
        $settings->slug_prefix = 'brandify';

        $slug = app(SlugGenerator::class)->suggest('Qualquer título');

        $this->assertStringStartsWith('brandify-', $slug);
    }

    public function test_manual_mode_returns_empty_string(): void
    {
        app(GeneralSettings::class)->slug_mode = 'manual';

        $slug = app(SlugGenerator::class)->suggest('Qualquer título');

        $this->assertSame('', $slug);
    }

    public function test_ensure_unique_appends_incremental_suffix_on_collision(): void
    {
        $directory = Directory::factory()->create();
        Proposal::factory()->for($directory)->create(['slug' => 'proposta']);
        Proposal::factory()->for($directory)->create(['slug' => 'proposta-1']);

        $slug = app(SlugGenerator::class)->ensureUnique('proposta');

        $this->assertSame('proposta-2', $slug);
    }

    public function test_ensure_unique_ignores_the_given_record_id(): void
    {
        $directory = Directory::factory()->create();
        $proposal = Proposal::factory()->for($directory)->create(['slug' => 'proposta']);

        $slug = app(SlugGenerator::class)->ensureUnique('proposta', ignoreId: $proposal->id);

        $this->assertSame('proposta', $slug);
    }
}
