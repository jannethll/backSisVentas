<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    /**
     * Registra un pago hecho por el cliente (queda 'pendiente' hasta que
     * el admin lo verifique manualmente).
     */
    public function registerPayment(Order $order, array $data, ?UploadedFile $proofImage): Payment
    {
        $imagePath = null;

        if ($proofImage) {
            $imagePath = $proofImage->store('payment-proofs', 'public');
        }

        return Payment::create([
            'order_id' => $order->id,
            'method' => $data['method'],
            'amount' => $data['amount'],
            'reference_code' => $data['reference_code'] ?? null,
            'proof_image_path' => $imagePath,
            'status' => 'pendiente',
        ]);
    }

    /**
     * El admin verifica o rechaza un pago. Si se verifica y la suma de
     * pagos verificados cubre el total del pedido, el pedido pasa a 'pagado'.
     */
    public function verifyPayment(Payment $payment, array $data, User $admin): Payment
    {
        return DB::transaction(function () use ($payment, $data, $admin) {
            $payment->update([
                'status' => $data['status'],
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'notes' => $data['notes'] ?? $payment->notes,
            ]);

            if ($data['status'] === 'verificado') {
                $this->maybeMarkOrderAsPaid($payment->order);
            }

            return $payment->fresh('order');
        });
    }

    private function maybeMarkOrderAsPaid(Order $order): void
    {
        $totalVerified = $order->payments()
            ->where('status', 'verificado')
            ->sum('amount');

        if ($totalVerified >= $order->total && $order->status === 'pendiente') {
            $order->update(['status' => 'pagado']);
        }
    }
}