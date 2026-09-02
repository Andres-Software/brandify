<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\Proposal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BackupCsvService
{
    private const DIRECTORY_COLUMNS = ['id', 'name', 'slug', 'created_at', 'updated_at'];

    private const PROPOSAL_COLUMNS = [
        'id', 'directory_id', 'slug', 'description', 'embed_src', 'embed_raw', 'expires_at', 'created_at', 'updated_at',
    ];

    /**
     * @return array{directories: string, proposals: string} Caminhos dos CSVs temporários gerados.
     */
    public function export(): array
    {
        return [
            'directories' => $this->writeCsv(self::DIRECTORY_COLUMNS, Directory::query()->orderBy('id')->get()),
            'proposals' => $this->writeCsv(self::PROPOSAL_COLUMNS, Proposal::query()->orderBy('id')->get()),
        ];
    }

    public function import(UploadedFile $directoriesCsv, UploadedFile $proposalsCsv): void
    {
        $directoryRows = $this->readCsv($directoriesCsv, self::DIRECTORY_COLUMNS);
        $proposalRows = $this->readCsv($proposalsCsv, self::PROPOSAL_COLUMNS);

        DB::transaction(function () use ($directoryRows, $proposalRows) {
            Proposal::query()->delete();
            Directory::query()->delete();

            if ($directoryRows !== []) {
                Directory::query()->insert($directoryRows);
                $this->syncSequence('directories');
            }

            if ($proposalRows !== []) {
                Proposal::query()->insert($proposalRows);
                $this->syncSequence('proposals');
            }
        });
    }

    /**
     * @param  string[]  $columns
     * @param  \Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model>  $rows
     */
    private function writeCsv(array $columns, $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'backup_csv_');

        $handle = fopen($path, 'w');
        fputcsv($handle, $columns, escape: '\\');

        foreach ($rows as $row) {
            fputcsv($handle, collect($columns)->map(fn (string $column) => $row->getAttribute($column))->all(), escape: '\\');
        }

        fclose($handle);

        return $path;
    }

    /**
     * @param  string[]  $expectedColumns
     * @return array<int, array<string, mixed>>
     */
    private function readCsv(UploadedFile $file, array $expectedColumns): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, escape: '\\');

        if ($header === false || $header !== $expectedColumns) {
            fclose($handle);

            throw new \InvalidArgumentException(
                'Cabeçalho do CSV inválido. Esperado: '.implode(',', $expectedColumns)
            );
        }

        $rows = [];

        while (($line = fgetcsv($handle, escape: '\\')) !== false) {
            $row = array_combine($header, $line);
            $rows[] = array_map(fn ($value) => $value === '' ? null : $value, $row);
        }

        fclose($handle);

        return $rows;
    }

    private function syncSequence(string $table): void
    {
        DB::statement(
            "SELECT setval(pg_get_serial_sequence('{$table}', 'id'), COALESCE((SELECT MAX(id) FROM {$table}), 1))"
        );
    }
}
