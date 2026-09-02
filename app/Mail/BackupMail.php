<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{directories: string, proposals: string}  $csvPaths
     */
    public function __construct(private readonly array $csvPaths)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Backup Brandify - '.now()->format('d/m/Y H:i'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.backup',
            with: ['generatedAt' => now()],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->csvPaths['directories'])->as('directories.csv')->withMime('text/csv'),
            Attachment::fromPath($this->csvPaths['proposals'])->as('proposals.csv')->withMime('text/csv'),
        ];
    }
}
