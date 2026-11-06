<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OrderItemBatch extends Model
{
    use HasUuid;

    const UPDATED_AT = null; // esta tabla no tiene updated_at

    protected $fillable = ['order_item_id', 'batch_id', 'quantity'];

    protected $casts = ['quantity' => 'decimal:2'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}