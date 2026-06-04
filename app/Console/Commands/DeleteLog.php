<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('app:delete-log {--months=1 : Supprime les fichiers de log plus vieux que ce nombre de mois}')]
#[Description("Supprime les fichiers de log de storage/logs de plus d'un mois")]
class DeleteLog extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $months = max(1, (int) $this->option('months'));
        $threshold = now()->subMonths($months);

        $directory = storage_path('logs');

        if (! File::isDirectory($directory)) {
            $this->warn("Le dossier {$directory} n'existe pas.");

            return self::SUCCESS;
        }

        $deleted = 0;

        foreach (File::files($directory) as $file) {
            if ($threshold->getTimestamp() <= $file->getMTime()) {
                continue;
            }

            File::delete($file->getPathname());
            $deleted++;
        }

        $this->info("{$deleted} fichier(s) de log supprimé(s) (antérieurs au {$threshold->toDateString()}).");

        return self::SUCCESS;
    }
}
