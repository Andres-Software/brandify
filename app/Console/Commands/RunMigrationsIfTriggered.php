<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RunMigrationsIfTriggered extends Command
{
    protected $signature = 'deploy:migrate-if-triggered';

    protected $description = 'Roda as migrations pendentes se o arquivo de gatilho existir (storage/app/migrate.trigger)';

    public function handle(): int
    {
        $triggerPath = storage_path('app/migrate.trigger');
        $lockPath = storage_path('app/migrate.lock');

        if (! File::exists($triggerPath)) {
            return self::SUCCESS;
        }

        if (File::exists($lockPath)) {
            $this->warn('Gatilho encontrado, mas já há uma migration em andamento (lock presente). Ignorando.');

            return self::SUCCESS;
        }

        File::put($lockPath, (string) now());

        try {
            // Removido antes de rodar: se a migration travar (timeout do host),
            // o próximo minuto não tenta rodar de novo em cima de um estado incerto.
            File::delete($triggerPath);

            $this->info('Gatilho encontrado. Rodando migrations...');

            $exitCode = Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            Log::info('Migration disparada via cron executada.', [
                'exit_code' => $exitCode,
                'output' => $output,
            ]);

            $this->line($output);

            return $exitCode === 0 ? self::SUCCESS : self::FAILURE;
        } catch (\Throwable $e) {
            Log::error('Falha ao rodar migration disparada via cron.', ['error' => $e->getMessage()]);
            $this->error('Falha ao rodar migrations: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            File::delete($lockPath);
        }
    }
}
