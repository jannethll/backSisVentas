<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBatchRequest;
use App\Http\Requests\Admin\UpdateBatchRequest;
use App\Http\Resources\AdminBatchResource;
use App\Models\Batch;
use App\Services\BatchService;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function __construct(private BatchService $batchService)
    {
    }

    // GET /api/v1/admin/batches?variant_id=&status=
    public function index(Request $request)
    {
        $batches = Batch::with('variant.product')
            ->when($request->variant_id, fn ($q, $id) => $q->where('product_variant_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest('production_date')
            ->paginate(20);

        return AdminBatchResource::collection($batches);
    }

    public function store(StoreBatchRequest $request)
    {
        $batch = $this->batchService->create($request->validated());

        return (new AdminBatchResource($batch))->response()->setStatusCode(201);
    }

    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        return new AdminBatchResource($this->batchService->update($batch, $request->validated()));
    }
}