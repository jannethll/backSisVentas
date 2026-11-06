<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_id', 'carrier_agency', 'destination_city', 'destination_agency',
        'tracking_number', 'pickup_code', 'recipient_name', 'recipient_phone',
        'shipping_cost', 'status', 'dispatched_at', 'estimated_delivery_date',
        'delivered_at', 'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'dispatched_at' => 'datetime',
        'estimated_delivery_date' => 'date',
        'delivered_at' => 'datetime',
    ];


    public const TRANSITIONS = [
    'preparando'        => ['enviado_a_agencia', 'incidencia'],
    'enviado_a_agencia' => ['en_transito', 'incidencia', 'devuelto'],
    'en_transito'       => ['entregado', 'incidencia', 'devuelto'],
    'entregado'         => [],
    'devuelto'          => ['preparando'],
    'incidencia'        => ['preparando', 'en_transito', 'devuelto'],
    ];

        public function nextStatuses(): array
        {
            return self::TRANSITIONS[$this->status] ?? [];
        }

        public function order()
        {
            return $this->belongsTo(Order::class);
        }

}