<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    // GET /api/v1/orders?status=pagado — cliente: solo los suyos; admin: todos
    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();

        $orders = Order::with(['items.variant', 'customer', 'shipment'])
            ->when($user->role === 'cliente', fn ($q) => $q->whereHas(
                'customer', fn ($c) => $c->where('user_id', $user->id)
            ))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    // POST /api/v1/orders
    public function store(CreateOrderRequest $request)
    {
        $user = Auth::guard('api')->user();
        $data = $request->validated();

        // Un cliente solo puede pedir a su propio nombre, sin importar lo que mande el navegador
        if ($user->role === 'cliente') {
            $data['customer_id'] = $user->customer?->id
                ?? abort(422, 'Tu cuenta no tiene un perfil de cliente.');
        }

        return new OrderResource($this->orderService->createOrder($data));
    }

    // GET /api/v1/orders/{order}
    public function show(Order $order)
    {
        $user = Auth::guard('api')->user();

        abort_if(
            $user->role === 'cliente' && $order->customer->user_id !== $user->id,
            403,
            'No autorizado.'
        );

        $order->load(['items.variant', 'items.batchAllocations.batch', 'customer', 'shipment']);

        return new OrderResource($order);
    }
}