<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_id', 'method', 'amount', 'reference_code', 'proof_image_path',
        'status', 'verified_by', 'verified_at', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}