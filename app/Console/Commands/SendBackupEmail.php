<?php

namespace App\Console\Commands;

use App\Mail\BackupMail;
use App\Services\BackupCsvService;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBackupEmail extends Command
{
    protected $signature = 'backup:send';

    protected $description = 'Gera um CSV com os dados do Brandify e envia por email';

    public function handle(BackupCsvService $csvService, GeneralSettings $settings): int
    {
        $email = $settings->backup_email;

        if (blank($email)) {
            $this->error('Nenhum email de backup configurado. Defina em Configurações > Email de backup.');

            return self::FAILURE;
        }

        $csvPaths = $csvService->export();

        try {
            Mail::to($email)->send(new BackupMail($csvPaths));

            Log::info('Backup enviado com sucesso.', ['email' => $email]);
            $this->info("Backup enviado para {$email}.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar backup.', ['email' => $email, 'error' => $e->getMessage()]);
            $this->error('Falha ao enviar backup: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            foreach ($csvPaths as $path) {
                @unlink($path);
            }
        }
    }
}
