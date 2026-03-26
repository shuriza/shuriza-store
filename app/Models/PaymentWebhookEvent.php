<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    protected $fillable = [
        'provider',
        'event_id',
        'order_number',
        'endpoint',
        'payload',
        'headers',
        'payload_hash',
        'status',
        'attempts',
        'processed_at',
        'error_message',
        'response_code',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
        'attempts' => 'integer',
        'response_code' => 'integer',
        'processed_at' => 'datetime',
    ];
}
