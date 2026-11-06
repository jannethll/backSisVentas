<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckExpiringBatches extends Command
{
    protected $signature = 'quesos:check-expiring-batches';
    protected $description = 'Notifica a los admins sobre lotes próximos a vencer';

    private const DAYS_AHEAD = 5;

    public function handle(NotificationService $notificationService): void
    {
        $batches = Batch::where('status', 'disponible')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays(self::DAYS_AHEAD))
            ->whereDate('expiration_date', '>=', now())
            ->get();

        foreach ($batches as $batch) {
            $notificationService->notifyAdmins('vencimientos', [
                'title' => 'Lote próximo a vencer',
                'message' => "Lote {$batch->batch_code} vence el {$batch->expiration_date->format('d/m/Y')}.",
                'batch_id' => $batch->id,
            ]);
        }

        $this->info('Revisión de vencimientos completada.');
    }
}