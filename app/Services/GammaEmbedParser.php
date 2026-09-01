<?php

namespace App\Services;

use App\Exceptions\InvalidEmbedException;
use App\Services\DTO\ParsedEmbed;
use DOMDocument;

class GammaEmbedParser
{
    /**
     * Parse a pasted <iframe> HTML snippet and extract its `src` and `title` attributes.
     *
     * @throws InvalidEmbedException
     */
    public function parse(string $html): ParsedEmbed
    {
        $html = trim($html);

        if ($html === '') {
            throw new InvalidEmbedException('Nenhum HTML foi informado.');
        }

        libxml_use_internal_errors(true);

        $document = new DOMDocument();
        $document->loadHTML(
            '<?xml encoding="utf-8" ?>'.$html,
            LIBXML_NOERROR | LIBXML_NOWARNING
        );

        libxml_use_internal_errors(false);

        $iframes = $document->getElementsByTagName('iframe');

        if ($iframes->length === 0) {
            throw new InvalidEmbedException('Nenhuma tag <iframe> foi encontrada no HTML informado.');
        }

        $iframe = $iframes->item(0);

        $src = trim((string) $iframe->getAttribute('src'));
        $title = trim((string) $iframe->getAttribute('title'));

        if ($src === '') {
            throw new InvalidEmbedException('O <iframe> encontrado não possui o atributo "src".');
        }

        return new ParsedEmbed(
            src: $src,
            title: $title !== '' ? $title : null,
        );
    }
}
