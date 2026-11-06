<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\CreateShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentStatusRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(private ShipmentService $shipmentService)
    {
    }

    // POST /api/v1/orders/{order}/shipment
    public function store(CreateShipmentRequest $request, Order $order)
    {
        $shipment = $this->shipmentService->createShipment($order, $request->validated());

        return new ShipmentResource($shipment->load('order'));
    }

    // PATCH /api/v1/shipments/{shipment}/status
    public function updateStatus(UpdateShipmentStatusRequest $request, Shipment $shipment)
    {
        $shipment = $this->shipmentService->updateStatus($shipment, $request->validated());

        return new ShipmentResource($shipment->load('order'));
    }

    // GET /api/v1/shipments — panel logístico: todos los envíos, filtrable por estado
    public function index(Request $request)
{
    $shipments = Shipment::with('order.customer')
        ->when($request->status, fn ($q, $status) => $q->where('status', $status))
        ->latest()
        ->paginate(20);

    return ShipmentResource::collection($shipments);
}
}