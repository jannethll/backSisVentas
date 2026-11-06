<?php

namespace App\Console\Commands;

use App\Models\ProductVariant;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'quesos:check-low-stock';
    protected $description = 'Notifica a los admins sobre variantes con stock bajo';

    private const THRESHOLD = 5; // ajustable según tu negocio

    public function handle(NotificationService $notificationService): void
    {
        $variants = ProductVariant::where('is_active', true)->get();

        foreach ($variants as $variant) {
            $stock = $variant->batches()->where('status', 'disponible')->sum('quantity_available');

            if ($stock > 0 && $stock <= self::THRESHOLD) {
                $notificationService->notifyAdmins('stock_bajo', [
                    'title' => 'Stock bajo',
                    'message' => "\"{$variant->variant_name}\" tiene solo {$stock} {$variant->sale_unit} disponibles.",
                    'variant_id' => $variant->id,
                ]);
            }
        }

        $this->info('Revisión de stock bajo completada.');
    }
}