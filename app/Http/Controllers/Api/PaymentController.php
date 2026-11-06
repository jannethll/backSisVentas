<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CreatePaymentRequest;
use App\Http\Requests\Payment\VerifyPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

// POST /api/v1/orders/{order}/payments
    public function store(CreatePaymentRequest $request, Order $order)
    {
        $user = Auth::guard('api')->user();
        
        abort_if(
            $user->role === 'cliente' && $order->customer->user_id !== $user->id,
            403,
            'No autorizado.'
        );

        $payment = $this->paymentService->registerPayment(
            $order,
            $request->validated(),
            $request->file('proof_image')
        );

        return new PaymentResource($payment->load('order'));
    }
    
    // PATCH /api/v1/payments/{payment}/verify — solo admin (protegido por middleware de ruta)
    public function verify(VerifyPaymentRequest $request, Payment $payment)
    {
        $admin = Auth::guard('api')->user();

        $payment = $this->paymentService->verifyPayment($payment, $request->validated(), $admin);

        return new PaymentResource($payment->load(['order', 'verifiedBy']));
    }

    // GET /api/v1/payments?status=pendiente — panel admin: pagos por revisar
    public function index()
    {
        $payments = Payment::with(['order', 'verifiedBy'])
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return PaymentResource::collection($payments);
    }
}