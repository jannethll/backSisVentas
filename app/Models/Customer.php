<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id', 'full_name', 'document_type', 'document_number',
        'phone', 'email', 'destination_city', 'address', 'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}