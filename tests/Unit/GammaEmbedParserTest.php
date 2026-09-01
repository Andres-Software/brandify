<?php

namespace Tests\Unit;

use App\Exceptions\InvalidEmbedException;
use App\Services\GammaEmbedParser;
use Tests\TestCase;

class GammaEmbedParserTest extends TestCase
{
    public function test_it_extracts_src_and_title_from_a_valid_iframe_snippet(): void
    {
        $html = '<iframe src="https://gamma.app/embed/dn28d4bvy5o25md" style="width: 700px; max-width: 100%; height: 450px" allow="fullscreen" title="O Fim da Burocracia Manual na Emissão de Notas"></iframe>';

        $parsed = (new GammaEmbedParser())->parse($html);

        $this->assertSame('https://gamma.app/embed/dn28d4bvy5o25md', $parsed->src);
        $this->assertSame('O Fim da Burocracia Manual na Emissão de Notas', $parsed->title);
    }

    public function test_it_throws_when_no_iframe_is_present(): void
    {
        $this->expectException(InvalidEmbedException::class);

        (new GammaEmbedParser())->parse('<div>Sem iframe aqui</div>');
    }

    public function test_it_throws_when_iframe_has_no_src(): void
    {
        $this->expectException(InvalidEmbedException::class);

        (new GammaEmbedParser())->parse('<iframe title="Sem src"></iframe>');
    }

    public function test_it_throws_when_html_is_empty(): void
    {
        $this->expectException(InvalidEmbedException::class);

        (new GammaEmbedParser())->parse('');
    }

    public function test_title_is_null_when_not_present(): void
    {
        $parsed = (new GammaEmbedParser())->parse('<iframe src="https://gamma.app/embed/abc123"></iframe>');

        $this->assertSame('https://gamma.app/embed/abc123', $parsed->src);
        $this->assertNull($parsed->title);
    }

    public function test_it_throws_when_src_uses_a_non_http_scheme(): void
    {
        $this->expectException(InvalidEmbedException::class);

        (new GammaEmbedParser())->parse('<iframe src="javascript:alert(1)"></iframe>');
    }

    public function test_it_throws_when_src_is_a_data_uri(): void
    {
        $this->expectException(InvalidEmbedException::class);

        (new GammaEmbedParser())->parse('<iframe src="data:text/html,<script>alert(1)</script>"></iframe>');
    }
}
